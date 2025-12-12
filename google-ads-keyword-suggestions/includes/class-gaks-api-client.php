<?php
/**
 * Google Ads API Client using OAuth Client JSON file
 */

if (!defined('ABSPATH')) {
    exit;
}

class GAKS_API_Client {
    
    const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';
    const API_BASE_URL = 'https://googleads.googleapis.com';
    const API_VERSION = 'v17';
    
    private $settings;
    private $credentials;
    
    public function __construct() {
        $this->settings = get_option('gaks_settings', array());
        $this->credentials = GAKS_Admin::get_oauth_credentials();
    }
    
    /**
     * Get access token using refresh token
     */
    public function get_access_token() {
        $cached = get_transient('gaks_access_token');
        if ($cached) return $cached;
        
        if (!$this->credentials || empty($this->credentials['client_id'])) {
            return new WP_Error('missing_credentials', __('OAuth JSON not configured. Please paste your client secret JSON file.', 'google-ads-keyword-suggestions'));
        }
        
        if (empty($this->settings['refresh_token'])) {
            return new WP_Error('missing_refresh_token', __('Refresh token is not configured.', 'google-ads-keyword-suggestions'));
        }
        
        $response = wp_remote_post($this->credentials['token_uri'], array(
            'body' => array(
                'client_id' => $this->credentials['client_id'],
                'client_secret' => $this->credentials['client_secret'],
                'refresh_token' => $this->settings['refresh_token'],
                'grant_type' => 'refresh_token'
            ),
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) return $response;
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return new WP_Error('oauth_error', sprintf(__('OAuth Error: %s', 'google-ads-keyword-suggestions'), $body['error_description'] ?? $body['error']));
        }
        
        if (!isset($body['access_token'])) {
            return new WP_Error('invalid_response', __('Invalid response from Google.', 'google-ads-keyword-suggestions'));
        }
        
        set_transient('gaks_access_token', $body['access_token'], 3000);
        return $body['access_token'];
    }
    
    /**
     * Make API request
     */
    public function request($endpoint, $method = 'POST', $body = null) {
        $access_token = $this->get_access_token();
        if (is_wp_error($access_token)) return $access_token;
        
        if (empty($this->settings['developer_token'])) {
            return new WP_Error('missing_developer_token', __('Developer token is not configured.', 'google-ads-keyword-suggestions'));
        }
        
        $customer_id = preg_replace('/[^0-9]/', '', $this->settings['customer_id']);
        if (empty($customer_id)) {
            return new WP_Error('missing_customer_id', __('Customer ID is not configured.', 'google-ads-keyword-suggestions'));
        }
        
        $url = sprintf('%s/%s/customers/%s/%s', self::API_BASE_URL, self::API_VERSION, $customer_id, $endpoint);
        
        $headers = array(
            'Authorization' => 'Bearer ' . $access_token,
            'developer-token' => $this->settings['developer_token'],
            'Content-Type' => 'application/json'
        );
        
        if (!empty($this->settings['login_customer_id'])) {
            $headers['login-customer-id'] = preg_replace('/[^0-9]/', '', $this->settings['login_customer_id']);
        }
        
        $args = array('method' => $method, 'headers' => $headers, 'timeout' => 60);
        if ($body !== null) $args['body'] = json_encode($body);
        
        $response = wp_remote_request($url, $args);
        if (is_wp_error($response)) return $response;
        
        $status_code = wp_remote_retrieve_response_code($response);
        $response_body = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($status_code >= 400) {
            $error_message = $response_body['error']['message'] ?? __('API request failed.', 'google-ads-keyword-suggestions');
            return new WP_Error('api_error', $error_message);
        }
        
        return $response_body;
    }
    
    /**
     * Test connection
     */
    public function test_connection() {
        $access_token = $this->get_access_token();
        if (is_wp_error($access_token)) return $access_token;
        
        $customer_id = preg_replace('/[^0-9]/', '', $this->settings['customer_id']);
        if (empty($customer_id)) {
            return new WP_Error('missing_customer_id', __('Customer ID is required.', 'google-ads-keyword-suggestions'));
        }
        
        if (empty($this->settings['developer_token'])) {
            return new WP_Error('missing_developer_token', __('Developer token is required.', 'google-ads-keyword-suggestions'));
        }
        
        $url = sprintf('%s/%s/customers/%s', self::API_BASE_URL, self::API_VERSION, $customer_id);
        
        $headers = array(
            'Authorization' => 'Bearer ' . $access_token,
            'developer-token' => $this->settings['developer_token']
        );
        
        if (!empty($this->settings['login_customer_id'])) {
            $headers['login-customer-id'] = preg_replace('/[^0-9]/', '', $this->settings['login_customer_id']);
        }
        
        $response = wp_remote_get($url, array('headers' => $headers, 'timeout' => 30));
        if (is_wp_error($response)) return $response;
        
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code >= 400) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            return new WP_Error('connection_failed', $body['error']['message'] ?? __('Connection failed.', 'google-ads-keyword-suggestions'));
        }
        
        return true;
    }
}

<?php
/**
 * Google Ads API Client using OAuth 2.0 Refresh Token
 * Works for ALL users - personal Gmail and organization accounts
 */

if (!defined('ABSPATH')) {
    exit;
}

class GAKS_API_Client {
    
    /**
     * Google OAuth token endpoint
     */
    const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';
    
    /**
     * Google Ads API base URL
     */
    const API_BASE_URL = 'https://googleads.googleapis.com';
    
    /**
     * API Version
     */
    const API_VERSION = 'v17';
    
    /**
     * Plugin settings
     */
    private $settings;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->settings = get_option('gaks_settings', array());
    }
    
    /**
     * Get access token using refresh token
     */
    public function get_access_token() {
        // Check for cached token
        $cached = get_transient('gaks_access_token');
        if ($cached) {
            return $cached;
        }
        
        if (empty($this->settings['refresh_token']) || 
            empty($this->settings['client_id']) || 
            empty($this->settings['client_secret'])) {
            return new WP_Error('missing_credentials', __('OAuth credentials are not configured. Please enter Client ID, Client Secret, and Refresh Token.', 'google-ads-keyword-suggestions'));
        }
        
        $response = wp_remote_post(self::TOKEN_ENDPOINT, array(
            'body' => array(
                'client_id' => $this->settings['client_id'],
                'client_secret' => $this->settings['client_secret'],
                'refresh_token' => $this->settings['refresh_token'],
                'grant_type' => 'refresh_token'
            ),
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            $error_desc = $body['error_description'] ?? $body['error'];
            return new WP_Error(
                'oauth_error',
                sprintf(__('OAuth Error: %s', 'google-ads-keyword-suggestions'), $error_desc)
            );
        }
        
        if (!isset($body['access_token'])) {
            return new WP_Error('invalid_response', __('Invalid response from Google OAuth server.', 'google-ads-keyword-suggestions'));
        }
        
        // Cache token (expires in ~1 hour, cache for 50 minutes)
        set_transient('gaks_access_token', $body['access_token'], 3000);
        
        return $body['access_token'];
    }
    
    /**
     * Make API request to Google Ads
     */
    public function request($endpoint, $method = 'POST', $body = null) {
        $access_token = $this->get_access_token();
        
        if (is_wp_error($access_token)) {
            return $access_token;
        }
        
        if (empty($this->settings['developer_token'])) {
            return new WP_Error('missing_developer_token', __('Developer token is not configured.', 'google-ads-keyword-suggestions'));
        }
        
        $customer_id = preg_replace('/[^0-9]/', '', $this->settings['customer_id']);
        
        if (empty($customer_id)) {
            return new WP_Error('missing_customer_id', __('Customer ID is not configured.', 'google-ads-keyword-suggestions'));
        }
        
        $url = sprintf('%s/%s/customers/%s/%s', 
            self::API_BASE_URL, 
            self::API_VERSION, 
            $customer_id, 
            $endpoint
        );
        
        $headers = array(
            'Authorization' => 'Bearer ' . $access_token,
            'developer-token' => $this->settings['developer_token'],
            'Content-Type' => 'application/json'
        );
        
        // Add login-customer-id header if using manager account
        if (!empty($this->settings['login_customer_id'])) {
            $login_customer_id = preg_replace('/[^0-9]/', '', $this->settings['login_customer_id']);
            $headers['login-customer-id'] = $login_customer_id;
        }
        
        $args = array(
            'method' => $method,
            'headers' => $headers,
            'timeout' => 60
        );
        
        if ($body !== null) {
            $args['body'] = json_encode($body);
        }
        
        $response = wp_remote_request($url, $args);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $response_body = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($status_code >= 400) {
            $error_message = __('API request failed.', 'google-ads-keyword-suggestions');
            
            if (isset($response_body['error']['message'])) {
                $error_message = $response_body['error']['message'];
            }
            
            return new WP_Error('api_error', $error_message, array('status' => $status_code));
        }
        
        return $response_body;
    }
    
    /**
     * Test API connection
     */
    public function test_connection() {
        $access_token = $this->get_access_token();
        
        if (is_wp_error($access_token)) {
            return $access_token;
        }
        
        $customer_id = preg_replace('/[^0-9]/', '', $this->settings['customer_id']);
        
        if (empty($customer_id)) {
            return new WP_Error('missing_customer_id', __('Customer ID is required.', 'google-ads-keyword-suggestions'));
        }
        
        if (empty($this->settings['developer_token'])) {
            return new WP_Error('missing_developer_token', __('Developer token is required.', 'google-ads-keyword-suggestions'));
        }
        
        $url = sprintf('%s/%s/customers/%s', 
            self::API_BASE_URL, 
            self::API_VERSION, 
            $customer_id
        );
        
        $headers = array(
            'Authorization' => 'Bearer ' . $access_token,
            'developer-token' => $this->settings['developer_token']
        );
        
        if (!empty($this->settings['login_customer_id'])) {
            $login_customer_id = preg_replace('/[^0-9]/', '', $this->settings['login_customer_id']);
            $headers['login-customer-id'] = $login_customer_id;
        }
        
        $response = wp_remote_get($url, array(
            'headers' => $headers,
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        
        if ($status_code >= 400) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $error_msg = isset($body['error']['message']) 
                ? $body['error']['message'] 
                : __('Connection test failed. Check your credentials.', 'google-ads-keyword-suggestions');
            return new WP_Error('connection_failed', $error_msg);
        }
        
        return true;
    }
    
    /**
     * Get settings
     */
    public function get_settings() {
        return $this->settings;
    }
}

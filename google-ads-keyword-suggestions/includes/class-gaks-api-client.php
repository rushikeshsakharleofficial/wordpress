<?php
/**
 * Google Ads API Client using Service Account authentication
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
     * Scope for Google Ads API
     */
    const SCOPE = 'https://www.googleapis.com/auth/adwords';
    
    /**
     * Plugin settings
     */
    private $settings;
    
    /**
     * Service account data
     */
    private $service_account;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->settings = get_option('gaks_settings', array());
        $this->load_service_account();
    }
    
    /**
     * Load service account from saved JSON
     */
    private function load_service_account() {
        $json_content = $this->settings['service_account_json'] ?? '';
        
        if (!empty($json_content)) {
            $this->service_account = json_decode($json_content, true);
        }
    }
    
    /**
     * Create JWT token for service account
     */
    private function create_jwt() {
        if (empty($this->service_account)) {
            return new WP_Error('no_service_account', __('Service account JSON is not configured.', 'google-ads-keyword-suggestions'));
        }
        
        $required_fields = ['client_email', 'private_key', 'token_uri'];
        foreach ($required_fields as $field) {
            if (empty($this->service_account[$field])) {
                return new WP_Error('invalid_service_account', sprintf(__('Service account JSON is missing required field: %s', 'google-ads-keyword-suggestions'), $field));
            }
        }
        
        $now = time();
        $expiry = $now + 3600; // 1 hour
        
        // JWT Header
        $header = array(
            'alg' => 'RS256',
            'typ' => 'JWT'
        );
        
        // JWT Claims
        $claims = array(
            'iss' => $this->service_account['client_email'],
            'sub' => $this->settings['impersonate_email'] ?? $this->service_account['client_email'],
            'scope' => self::SCOPE,
            'aud' => $this->service_account['token_uri'],
            'iat' => $now,
            'exp' => $expiry
        );
        
        // Encode header and claims
        $header_encoded = $this->base64url_encode(json_encode($header));
        $claims_encoded = $this->base64url_encode(json_encode($claims));
        
        // Create signature
        $signature_input = $header_encoded . '.' . $claims_encoded;
        
        $private_key = openssl_pkey_get_private($this->service_account['private_key']);
        if (!$private_key) {
            return new WP_Error('invalid_private_key', __('Invalid private key in service account JSON.', 'google-ads-keyword-suggestions'));
        }
        
        $signature = '';
        $success = openssl_sign($signature_input, $signature, $private_key, OPENSSL_ALGO_SHA256);
        
        if (!$success) {
            return new WP_Error('signing_failed', __('Failed to sign JWT token.', 'google-ads-keyword-suggestions'));
        }
        
        $signature_encoded = $this->base64url_encode($signature);
        
        return $header_encoded . '.' . $claims_encoded . '.' . $signature_encoded;
    }
    
    /**
     * Base64 URL encode
     */
    private function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Get access token using service account
     */
    public function get_access_token() {
        // Check for cached token
        $cached = get_transient('gaks_access_token');
        if ($cached) {
            return $cached;
        }
        
        $jwt = $this->create_jwt();
        
        if (is_wp_error($jwt)) {
            return $jwt;
        }
        
        $token_uri = $this->service_account['token_uri'] ?? self::TOKEN_ENDPOINT;
        
        $response = wp_remote_post($token_uri, array(
            'body' => array(
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ),
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($body['error'])) {
            return new WP_Error(
                'oauth_error',
                sprintf(__('OAuth Error: %s - %s', 'google-ads-keyword-suggestions'), 
                    $body['error'], 
                    $body['error_description'] ?? ''
                )
            );
        }
        
        if (!isset($body['access_token'])) {
            return new WP_Error('invalid_response', __('Invalid response from OAuth server.', 'google-ads-keyword-suggestions'));
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
        
        // Try to make a simple API call
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
        
        // Add login-customer-id header if using manager account
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
                : __('Connection test failed.', 'google-ads-keyword-suggestions');
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
    
    /**
     * Validate service account JSON
     */
    public static function validate_service_account_json($json_string) {
        if (empty($json_string)) {
            return new WP_Error('empty_json', __('Service account JSON is empty.', 'google-ads-keyword-suggestions'));
        }
        
        $data = json_decode($json_string, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_json', __('Invalid JSON format.', 'google-ads-keyword-suggestions'));
        }
        
        $required_fields = ['type', 'project_id', 'private_key_id', 'private_key', 'client_email', 'client_id', 'token_uri'];
        $missing = array();
        
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            return new WP_Error('missing_fields', sprintf(__('Missing required fields: %s', 'google-ads-keyword-suggestions'), implode(', ', $missing)));
        }
        
        if ($data['type'] !== 'service_account') {
            return new WP_Error('wrong_type', __('JSON must be a service account key file (type should be "service_account").', 'google-ads-keyword-suggestions'));
        }
        
        return true;
    }
}

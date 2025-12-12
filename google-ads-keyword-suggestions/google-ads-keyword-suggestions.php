<?php
/**
 * Plugin Name: Google Ads Keyword Suggestions
 * Plugin URI: https://github.com/rushikesh-sakharle/google-ads-keyword-suggestions
 * Description: Get keyword suggestions from Google Ads API while writing blog posts to optimize your SEO strategy. Uses GCP Service Account for authentication.
 * Version: 1.0.3
 * Author: Rushikesh Sakharle
 * Author URI: https://rushikesh.dev
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: google-ads-keyword-suggestions
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('GAKS_VERSION', '1.0.3');
define('GAKS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GAKS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GAKS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class Google_Ads_Keyword_Suggestions {
    
    /**
     * Single instance of the class
     */
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    /**
     * Load required files
     */
    private function load_dependencies() {
        require_once GAKS_PLUGIN_DIR . 'includes/class-gaks-admin.php';
        require_once GAKS_PLUGIN_DIR . 'includes/class-gaks-api-client.php';
        require_once GAKS_PLUGIN_DIR . 'includes/class-gaks-keyword-service.php';
        require_once GAKS_PLUGIN_DIR . 'includes/class-gaks-setup-guide.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Initialize admin settings
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Enqueue editor scripts
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
        
        // Register REST API endpoints
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        
        // Admin styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Keyword Suggestions', 'google-ads-keyword-suggestions'),
            __('Keyword Suggestions', 'google-ads-keyword-suggestions'),
            'manage_options',
            'gaks-settings',
            array('GAKS_Admin', 'render_settings_page')
        );
        
        // Add Setup Guide page
        add_menu_page(
            __('Keywords Setup', 'google-ads-keyword-suggestions'),
            __('Keywords Setup', 'google-ads-keyword-suggestions'),
            'manage_options',
            'gaks-setup-guide',
            array('GAKS_Setup_Guide', 'render_page'),
            'dashicons-welcome-learn-more',
            100
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        GAKS_Admin::register_settings();
    }
    
    /**
     * Enqueue block editor assets
     */
    public function enqueue_editor_assets() {
        wp_enqueue_script(
            'gaks-editor-sidebar',
            GAKS_PLUGIN_URL . 'assets/js/editor-sidebar.js',
            array('wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-api-fetch'),
            GAKS_VERSION,
            true
        );
        
        wp_localize_script('gaks-editor-sidebar', 'gaksData', array(
            'restUrl' => rest_url('gaks/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'isConfigured' => GAKS_Admin::is_configured()
        ));
        
        wp_enqueue_style(
            'gaks-editor-style',
            GAKS_PLUGIN_URL . 'assets/css/editor-style.css',
            array(),
            GAKS_VERSION
        );
    }
    
    /**
     * Enqueue admin styles
     */
    public function enqueue_admin_styles($hook) {
        $allowed_hooks = array('settings_page_gaks-settings', 'toplevel_page_gaks-setup-guide');
        
        if (!in_array($hook, $allowed_hooks)) {
            return;
        }
        
        wp_enqueue_style(
            'gaks-admin-style',
            GAKS_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            GAKS_VERSION
        );
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('gaks/v1', '/suggestions', array(
            'methods' => 'POST',
            'callback' => array($this, 'get_keyword_suggestions'),
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            },
            'args' => array(
                'keywords' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field'
                )
            )
        ));
        
        register_rest_route('gaks/v1', '/test-connection', array(
            'methods' => 'POST',
            'callback' => array($this, 'test_api_connection'),
            'permission_callback' => function() {
                return current_user_can('manage_options');
            }
        ));
    }
    
    /**
     * REST endpoint: Get keyword suggestions
     */
    public function get_keyword_suggestions($request) {
        $keywords = $request->get_param('keywords');
        
        if (empty($keywords)) {
            return new WP_Error('invalid_keywords', __('Please provide seed keywords.', 'google-ads-keyword-suggestions'), array('status' => 400));
        }
        
        $keyword_service = new GAKS_Keyword_Service();
        $result = $keyword_service->generate_keyword_ideas($keywords);
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        return rest_ensure_response($result);
    }
    
    /**
     * REST endpoint: Test API connection
     */
    public function test_api_connection($request) {
        $api_client = new GAKS_API_Client();
        $result = $api_client->test_connection();
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        return rest_ensure_response(array(
            'success' => true,
            'message' => __('Connection successful!', 'google-ads-keyword-suggestions')
        ));
    }
    
    /**
     * Activation hook
     */
    public static function activate() {
        // Set default options
        $defaults = array(
            'developer_token' => '',
            'client_id' => '',
            'client_secret' => '',
            'refresh_token' => '',
            'customer_id' => '',
            'location_id' => '2840', // United States
            'language_id' => '1000'  // English
        );
        
        add_option('gaks_settings', $defaults);
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Deactivation hook
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }
}

// Activation/Deactivation hooks
register_activation_hook(__FILE__, array('Google_Ads_Keyword_Suggestions', 'activate'));
register_deactivation_hook(__FILE__, array('Google_Ads_Keyword_Suggestions', 'deactivate'));

// Initialize plugin
add_action('plugins_loaded', function() {
    Google_Ads_Keyword_Suggestions::get_instance();
});

<?php
/**
 * Admin settings page - Uses OAuth Client JSON file
 */

if (!defined('ABSPATH')) {
    exit;
}

class GAKS_Admin {
    
    /**
     * Render the settings page
     */
    public static function render_settings_page() {
        $settings = get_option('gaks_settings', array());
        
        // Parse saved JSON for display
        $client_info = null;
        if (!empty($settings['client_json'])) {
            $json_data = json_decode($settings['client_json'], true);
            if ($json_data) {
                $installed = $json_data['installed'] ?? $json_data['web'] ?? $json_data;
                $client_info = array(
                    'client_id' => $installed['client_id'] ?? '',
                    'project_id' => $installed['project_id'] ?? ''
                );
            }
        }
        ?>
        <div class="wrap gaks-settings-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="gaks-settings-container">
                <div class="gaks-settings-main">
                    <form method="post" action="options.php">
                        <?php settings_fields('gaks_settings_group'); ?>
                        
                        <!-- OAuth Client JSON Card -->
                        <div class="gaks-card">
                            <h2 class="gaks-card-title">
                                <span class="dashicons dashicons-cloud"></span>
                                <?php _e('OAuth Client Credentials', 'google-ads-keyword-suggestions'); ?>
                            </h2>
                            
                            <?php if ($client_info && !empty($client_info['client_id'])): ?>
                                <div class="gaks-service-account-info">
                                    <div class="gaks-sa-status success">
                                        <span class="dashicons dashicons-yes-alt"></span>
                                        <?php _e('OAuth Credentials Loaded', 'google-ads-keyword-suggestions'); ?>
                                    </div>
                                    <div class="gaks-sa-details">
                                        <div class="gaks-sa-detail">
                                            <span class="label"><?php _e('Client ID:', 'google-ads-keyword-suggestions'); ?></span>
                                            <span class="value"><?php echo esc_html(substr($client_info['client_id'], 0, 30) . '...'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <table class="form-table gaks-form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="client_json"><?php _e('Client Secret JSON', 'google-ads-keyword-suggestions'); ?></label>
                                    </th>
                                    <td>
                                        <textarea 
                                            id="client_json" 
                                            name="gaks_settings[client_json]" 
                                            rows="6"
                                            class="large-text code gaks-json-input"
                                            placeholder='<?php _e('Paste the contents of your downloaded JSON file here...', 'google-ads-keyword-suggestions'); ?>'
                                        ><?php echo esc_textarea($settings['client_json'] ?? ''); ?></textarea>
                                        <p class="description">
                                            <?php _e('Click "Download JSON" in Google Cloud Console and paste the file contents here.', 'google-ads-keyword-suggestions'); ?>
                                        </p>
                                        <div id="gaks-json-validation" class="gaks-validation-result"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="refresh_token"><?php _e('Refresh Token', 'google-ads-keyword-suggestions'); ?></label>
                                    </th>
                                    <td>
                                        <input type="password" 
                                               id="refresh_token" 
                                               name="gaks_settings[refresh_token]" 
                                               value="<?php echo esc_attr($settings['refresh_token'] ?? ''); ?>"
                                               class="large-text gaks-input"
                                               autocomplete="off">
                                        <p class="description">
                                            <?php _e('Generate using', 'google-ads-keyword-suggestions'); ?> 
                                            <a href="https://developers.google.com/oauthplayground/" target="_blank"><?php _e('OAuth Playground', 'google-ads-keyword-suggestions'); ?></a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Google Ads Settings Card -->
                        <div class="gaks-card">
                            <h2 class="gaks-card-title">
                                <span class="dashicons dashicons-megaphone"></span>
                                <?php _e('Google Ads Settings', 'google-ads-keyword-suggestions'); ?>
                            </h2>
                            
                            <table class="form-table gaks-form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="developer_token"><?php _e('Developer Token', 'google-ads-keyword-suggestions'); ?></label>
                                    </th>
                                    <td>
                                        <input type="password" 
                                               id="developer_token" 
                                               name="gaks_settings[developer_token]" 
                                               value="<?php echo esc_attr($settings['developer_token'] ?? ''); ?>"
                                               class="regular-text gaks-input" 
                                               autocomplete="off">
                                        <p class="description"><?php _e('From Google Ads → Tools & Settings → API Center', 'google-ads-keyword-suggestions'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="customer_id"><?php _e('Customer ID', 'google-ads-keyword-suggestions'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" 
                                               id="customer_id" 
                                               name="gaks_settings[customer_id]" 
                                               value="<?php echo esc_attr($settings['customer_id'] ?? ''); ?>"
                                               class="regular-text gaks-input"
                                               placeholder="123-456-7890"
                                               autocomplete="off">
                                        <p class="description"><?php _e('Your account ID (top-right of Google Ads)', 'google-ads-keyword-suggestions'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="login_customer_id"><?php _e('Login Customer ID', 'google-ads-keyword-suggestions'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" 
                                               id="login_customer_id" 
                                               name="gaks_settings[login_customer_id]" 
                                               value="<?php echo esc_attr($settings['login_customer_id'] ?? ''); ?>"
                                               class="regular-text gaks-input"
                                               placeholder="Optional"
                                               autocomplete="off">
                                        <p class="description"><?php _e('Only if using Manager Account (MCC)', 'google-ads-keyword-suggestions'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Targeting Settings Card -->
                        <div class="gaks-card">
                            <h2 class="gaks-card-title">
                                <span class="dashicons dashicons-location"></span>
                                <?php _e('Targeting', 'google-ads-keyword-suggestions'); ?>
                            </h2>
                            
                            <table class="form-table gaks-form-table">
                                <tr>
                                    <th><label for="location_id"><?php _e('Location', 'google-ads-keyword-suggestions'); ?></label></th>
                                    <td>
                                        <select id="location_id" name="gaks_settings[location_id]" class="gaks-select">
                                            <option value="2840" <?php selected(($settings['location_id'] ?? '2840'), '2840'); ?>>United States</option>
                                            <option value="2826" <?php selected(($settings['location_id'] ?? ''), '2826'); ?>>United Kingdom</option>
                                            <option value="2124" <?php selected(($settings['location_id'] ?? ''), '2124'); ?>>Canada</option>
                                            <option value="2036" <?php selected(($settings['location_id'] ?? ''), '2036'); ?>>Australia</option>
                                            <option value="2356" <?php selected(($settings['location_id'] ?? ''), '2356'); ?>>India</option>
                                            <option value="2276" <?php selected(($settings['location_id'] ?? ''), '2276'); ?>>Germany</option>
                                            <option value="2250" <?php selected(($settings['location_id'] ?? ''), '2250'); ?>>France</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="language_id"><?php _e('Language', 'google-ads-keyword-suggestions'); ?></label></th>
                                    <td>
                                        <select id="language_id" name="gaks_settings[language_id]" class="gaks-select">
                                            <option value="1000" <?php selected(($settings['language_id'] ?? '1000'), '1000'); ?>>English</option>
                                            <option value="1001" <?php selected(($settings['language_id'] ?? ''), '1001'); ?>>German</option>
                                            <option value="1002" <?php selected(($settings['language_id'] ?? ''), '1002'); ?>>French</option>
                                            <option value="1003" <?php selected(($settings['language_id'] ?? ''), '1003'); ?>>Spanish</option>
                                            <option value="1017" <?php selected(($settings['language_id'] ?? ''), '1017'); ?>>Hindi</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="gaks-actions">
                            <?php submit_button(__('Save Settings', 'google-ads-keyword-suggestions'), 'primary', 'submit', false); ?>
                            <button type="button" id="gaks-test-connection" class="button button-secondary">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <?php _e('Test Connection', 'google-ads-keyword-suggestions'); ?>
                            </button>
                            <span id="gaks-test-result" class="gaks-test-result"></span>
                        </div>
                    </form>
                </div>
                
                <div class="gaks-settings-sidebar">
                    <div class="gaks-card gaks-help-card">
                        <h3><?php _e('Quick Setup', 'google-ads-keyword-suggestions'); ?></h3>
                        <ol>
                            <li><?php _e('Download OAuth JSON', 'google-ads-keyword-suggestions'); ?></li>
                            <li><?php _e('Get refresh token', 'google-ads-keyword-suggestions'); ?></li>
                            <li><?php _e('Get developer token', 'google-ads-keyword-suggestions'); ?></li>
                            <li><?php _e('Paste all here', 'google-ads-keyword-suggestions'); ?></li>
                        </ol>
                        <a href="<?php echo admin_url('admin.php?page=gaks-setup-guide'); ?>" class="button button-link">
                            <?php _e('Full Setup Guide', 'google-ads-keyword-suggestions'); ?> →
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#gaks-test-connection').on('click', function() {
                var $btn = $(this);
                var $result = $('#gaks-test-result');
                
                $btn.prop('disabled', true);
                $result.removeClass('success error').text('Testing...');
                
                $.ajax({
                    url: '<?php echo rest_url('gaks/v1/test-connection'); ?>',
                    method: 'POST',
                    headers: { 'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>' },
                    success: function(response) {
                        $result.addClass('success').text(response.message);
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Connection failed';
                        $result.addClass('error').text(msg);
                    },
                    complete: function() { $btn.prop('disabled', false); }
                });
            });
            
            $('#client_json').on('blur', function() {
                var json = $(this).val().trim();
                var $v = $('#gaks-json-validation');
                if (!json) { $v.empty(); return; }
                try {
                    var data = JSON.parse(json);
                    var installed = data.installed || data.web || data;
                    if (installed.client_id && installed.client_secret) {
                        $v.html('<span class="success"><span class="dashicons dashicons-yes"></span> Valid OAuth credentials</span>');
                    } else {
                        $v.html('<span class="error"><span class="dashicons dashicons-no"></span> Missing client_id or client_secret</span>');
                    }
                } catch (e) {
                    $v.html('<span class="error"><span class="dashicons dashicons-no"></span> Invalid JSON</span>');
                }
            });
        });
        </script>
        
        <style>
        .gaks-json-input { font-family: monospace !important; font-size: 12px !important; background: #1e293b !important; color: #e2e8f0 !important; border-radius: 8px !important; padding: 12px !important; }
        .gaks-service-account-info { margin-bottom: 16px; padding: 12px 16px; background: #f0fdf4; border-radius: 8px; border: 1px solid #86efac; }
        .gaks-sa-status { display: flex; align-items: center; gap: 8px; font-weight: 600; color: #166534; }
        .gaks-sa-status .dashicons { color: #22c55e; }
        .gaks-sa-details { margin-top: 8px; }
        .gaks-sa-detail .label { color: #64748b; margin-right: 8px; font-size: 13px; }
        .gaks-sa-detail .value { font-family: monospace; color: #1e293b; font-size: 12px; }
        .gaks-validation-result { margin-top: 8px; }
        .gaks-validation-result .success { color: #166534; display: flex; align-items: center; gap: 4px; }
        .gaks-validation-result .error { color: #991b1b; display: flex; align-items: center; gap: 4px; }
        </style>
        <?php
    }
    
    /**
     * Register settings
     */
    public static function register_settings() {
        register_setting('gaks_settings_group', 'gaks_settings', array(
            'type' => 'array',
            'sanitize_callback' => array(__CLASS__, 'sanitize_settings')
        ));
    }
    
    /**
     * Sanitize settings
     */
    public static function sanitize_settings($input) {
        $sanitized = array();
        
        $sanitized['client_json'] = $input['client_json'] ?? '';
        $sanitized['refresh_token'] = sanitize_text_field($input['refresh_token'] ?? '');
        $sanitized['developer_token'] = sanitize_text_field($input['developer_token'] ?? '');
        $sanitized['customer_id'] = preg_replace('/[^0-9]/', '', $input['customer_id'] ?? '');
        $sanitized['login_customer_id'] = preg_replace('/[^0-9]/', '', $input['login_customer_id'] ?? '');
        $sanitized['location_id'] = sanitize_text_field($input['location_id'] ?? '2840');
        $sanitized['language_id'] = sanitize_text_field($input['language_id'] ?? '1000');
        
        delete_transient('gaks_access_token');
        
        return $sanitized;
    }
    
    /**
     * Check if plugin is configured
     */
    public static function is_configured() {
        $settings = get_option('gaks_settings', array());
        
        if (empty($settings['client_json']) || empty($settings['refresh_token']) || 
            empty($settings['developer_token']) || empty($settings['customer_id'])) {
            return false;
        }
        
        $json_data = json_decode($settings['client_json'], true);
        if (!$json_data) return false;
        
        $installed = $json_data['installed'] ?? $json_data['web'] ?? $json_data;
        return !empty($installed['client_id']) && !empty($installed['client_secret']);
    }
    
    /**
     * Get OAuth credentials from JSON
     */
    public static function get_oauth_credentials() {
        $settings = get_option('gaks_settings', array());
        
        if (empty($settings['client_json'])) {
            return null;
        }
        
        $json_data = json_decode($settings['client_json'], true);
        if (!$json_data) return null;
        
        $installed = $json_data['installed'] ?? $json_data['web'] ?? $json_data;
        
        return array(
            'client_id' => $installed['client_id'] ?? '',
            'client_secret' => $installed['client_secret'] ?? '',
            'token_uri' => $installed['token_uri'] ?? 'https://oauth2.googleapis.com/token'
        );
    }
}

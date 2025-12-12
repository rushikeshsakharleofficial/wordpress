<?php
/**
 * Admin settings page for the plugin - Service Account Version
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
        $has_service_account = !empty($settings['service_account_json']);
        
        // Get service account info for display
        $service_account_info = null;
        if ($has_service_account) {
            $sa_data = json_decode($settings['service_account_json'], true);
            if ($sa_data) {
                $service_account_info = array(
                    'email' => $sa_data['client_email'] ?? '',
                    'project' => $sa_data['project_id'] ?? ''
                );
            }
        }
        ?>
        <div class="wrap gaks-settings-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="gaks-settings-container">
                <div class="gaks-settings-main">
                    <form method="post" action="options.php" enctype="multipart/form-data">
                        <?php settings_fields('gaks_settings_group'); ?>
                        
                        <!-- Service Account Card -->
                        <div class="gaks-card">
                            <h2 class="gaks-card-title">
                                <span class="dashicons dashicons-cloud"></span>
                                <?php _e('GCP Service Account', 'google-ads-keyword-suggestions'); ?>
                            </h2>
                            
                            <?php if ($has_service_account && $service_account_info): ?>
                                <div class="gaks-service-account-info">
                                    <div class="gaks-sa-status success">
                                        <span class="dashicons dashicons-yes-alt"></span>
                                        <?php _e('Service Account Configured', 'google-ads-keyword-suggestions'); ?>
                                    </div>
                                    <div class="gaks-sa-details">
                                        <div class="gaks-sa-detail">
                                            <span class="label"><?php _e('Project:', 'google-ads-keyword-suggestions'); ?></span>
                                            <span class="value"><?php echo esc_html($service_account_info['project']); ?></span>
                                        </div>
                                        <div class="gaks-sa-detail">
                                            <span class="label"><?php _e('Email:', 'google-ads-keyword-suggestions'); ?></span>
                                            <span class="value"><?php echo esc_html($service_account_info['email']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <table class="form-table gaks-form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="service_account_json"><?php _e('Service Account JSON', 'google-ads-keyword-suggestions'); ?></label>
                                    </th>
                                    <td>
                                        <textarea 
                                            id="service_account_json" 
                                            name="gaks_settings[service_account_json]" 
                                            rows="6"
                                            class="large-text code gaks-json-input"
                                            placeholder='<?php _e('Paste your service account JSON key here...', 'google-ads-keyword-suggestions'); ?>'
                                        ><?php echo esc_textarea($settings['service_account_json'] ?? ''); ?></textarea>
                                        <p class="description">
                                            <?php _e('Paste the contents of your GCP service account JSON key file.', 'google-ads-keyword-suggestions'); ?>
                                            <a href="https://console.cloud.google.com/iam-admin/serviceaccounts" target="_blank"><?php _e('Create Service Account', 'google-ads-keyword-suggestions'); ?></a>
                                        </p>
                                        <div id="gaks-json-validation" class="gaks-validation-result"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="impersonate_email"><?php _e('Impersonate Email', 'google-ads-keyword-suggestions'); ?></label>
                                    </th>
                                    <td>
                                        <input type="email" 
                                               id="impersonate_email" 
                                               name="gaks_settings[impersonate_email]" 
                                               value="<?php echo esc_attr($settings['impersonate_email'] ?? ''); ?>"
                                               class="regular-text gaks-input"
                                               placeholder="admin@yourdomain.com">
                                        <p class="description"><?php _e('Email of the user to impersonate (must have Google Ads access). Leave empty to use service account email.', 'google-ads-keyword-suggestions'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- API Credentials Card -->
                        <div class="gaks-card">
                            <h2 class="gaks-card-title">
                                <span class="dashicons dashicons-admin-network"></span>
                                <?php _e('Google Ads API Settings', 'google-ads-keyword-suggestions'); ?>
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
                                        <p class="description"><?php _e('Get this from your Google Ads Manager account under API Center.', 'google-ads-keyword-suggestions'); ?></p>
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
                                        <p class="description"><?php _e('Your Google Ads customer ID (with or without dashes).', 'google-ads-keyword-suggestions'); ?></p>
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
                                               placeholder="123-456-7890"
                                               autocomplete="off">
                                        <p class="description"><?php _e('Manager account ID (only if accessing via a manager account). Leave empty if not using a manager account.', 'google-ads-keyword-suggestions'); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Targeting Settings Card -->
                        <div class="gaks-card">
                            <h2 class="gaks-card-title">
                                <span class="dashicons dashicons-location"></span>
                                <?php _e('Targeting Settings', 'google-ads-keyword-suggestions'); ?>
                            </h2>
                            
                            <table class="form-table gaks-form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="location_id"><?php _e('Target Location', 'google-ads-keyword-suggestions'); ?></label>
                                    </th>
                                    <td>
                                        <select id="location_id" name="gaks_settings[location_id]" class="gaks-select">
                                            <option value="2840" <?php selected(($settings['location_id'] ?? '2840'), '2840'); ?>><?php _e('United States', 'google-ads-keyword-suggestions'); ?></option>
                                            <option value="2826" <?php selected(($settings['location_id'] ?? ''), '2826'); ?>><?php _e('United Kingdom', 'google-ads-keyword-suggestions'); ?></option>
                                            <option value="2124" <?php selected(($settings['location_id'] ?? ''), '2124'); ?>><?php _e('Canada', 'google-ads-keyword-suggestions'); ?></option>
                                            <option value="2036" <?php selected(($settings['location_id'] ?? ''), '2036'); ?>><?php _e('Australia', 'google-ads-keyword-suggestions'); ?></option>
                                            <option value="2356" <?php selected(($settings['location_id'] ?? ''), '2356'); ?>><?php _e('India', 'google-ads-keyword-suggestions'); ?></option>
                                            <option value="2276" <?php selected(($settings['location_id'] ?? ''), '2276'); ?>><?php _e('Germany', 'google-ads-keyword-suggestions'); ?></option>
                                            <option value="2250" <?php selected(($settings['location_id'] ?? ''), '2250'); ?>><?php _e('France', 'google-ads-keyword-suggestions'); ?></option>
                                        </select>
                                        <p class="description"><?php _e('Geographic location for keyword suggestions.', 'google-ads-keyword-suggestions'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="language_id"><?php _e('Language', 'google-ads-keyword-suggestions'); ?></label>
                                    </th>
                                    <td>
                                        <select id="language_id" name="gaks_settings[language_id]" class="gaks-select">
                                            <option value="1000" <?php selected(($settings['language_id'] ?? '1000'), '1000'); ?>><?php _e('English', 'google-ads-keyword-suggestions'); ?></option>
                                            <option value="1001" <?php selected(($settings['language_id'] ?? ''), '1001'); ?>><?php _e('German', 'google-ads-keyword-suggestions'); ?></option>
                                            <option value="1002" <?php selected(($settings['language_id'] ?? ''), '1002'); ?>><?php _e('French', 'google-ads-keyword-suggestions'); ?></option>
                                            <option value="1003" <?php selected(($settings['language_id'] ?? ''), '1003'); ?>><?php _e('Spanish', 'google-ads-keyword-suggestions'); ?></option>
                                            <option value="1017" <?php selected(($settings['language_id'] ?? ''), '1017'); ?>><?php _e('Hindi', 'google-ads-keyword-suggestions'); ?></option>
                                            <option value="1020" <?php selected(($settings['language_id'] ?? ''), '1020'); ?>><?php _e('Portuguese', 'google-ads-keyword-suggestions'); ?></option>
                                        </select>
                                        <p class="description"><?php _e('Target language for keyword suggestions.', 'google-ads-keyword-suggestions'); ?></p>
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
                        <h3><?php _e('Setup Guide', 'google-ads-keyword-suggestions'); ?></h3>
                        <ol>
                            <li><?php _e('Create a GCP Service Account', 'google-ads-keyword-suggestions'); ?></li>
                            <li><?php _e('Enable Google Ads API', 'google-ads-keyword-suggestions'); ?></li>
                            <li><?php _e('Download JSON key file', 'google-ads-keyword-suggestions'); ?></li>
                            <li><?php _e('Get Developer Token from Google Ads', 'google-ads-keyword-suggestions'); ?></li>
                            <li><?php _e('Paste JSON and configure here', 'google-ads-keyword-suggestions'); ?></li>
                        </ol>
                        <a href="<?php echo admin_url('admin.php?page=gaks-setup-guide'); ?>" class="button button-link">
                            <?php _e('View Full Setup Guide', 'google-ads-keyword-suggestions'); ?> →
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Test connection
            $('#gaks-test-connection').on('click', function() {
                var $btn = $(this);
                var $result = $('#gaks-test-result');
                
                $btn.prop('disabled', true);
                $result.removeClass('success error').text('<?php _e('Testing...', 'google-ads-keyword-suggestions'); ?>');
                
                $.ajax({
                    url: '<?php echo rest_url('gaks/v1/test-connection'); ?>',
                    method: 'POST',
                    headers: {
                        'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                    },
                    success: function(response) {
                        $result.addClass('success').text(response.message);
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message 
                            ? xhr.responseJSON.message 
                            : '<?php _e('Connection failed', 'google-ads-keyword-suggestions'); ?>';
                        $result.addClass('error').text(msg);
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });
            
            // Validate JSON on input
            $('#service_account_json').on('blur', function() {
                var json = $(this).val().trim();
                var $validation = $('#gaks-json-validation');
                
                if (!json) {
                    $validation.empty();
                    return;
                }
                
                try {
                    var data = JSON.parse(json);
                    if (data.type === 'service_account' && data.client_email && data.private_key) {
                        $validation.html('<span class="success"><span class="dashicons dashicons-yes"></span> <?php _e('Valid service account JSON', 'google-ads-keyword-suggestions'); ?></span>');
                    } else {
                        $validation.html('<span class="error"><span class="dashicons dashicons-no"></span> <?php _e('Invalid: Must be a service account key file', 'google-ads-keyword-suggestions'); ?></span>');
                    }
                } catch (e) {
                    $validation.html('<span class="error"><span class="dashicons dashicons-no"></span> <?php _e('Invalid JSON format', 'google-ads-keyword-suggestions'); ?></span>');
                }
            });
        });
        </script>
        
        <style>
        .gaks-json-input {
            font-family: 'Monaco', 'Consolas', monospace !important;
            font-size: 12px !important;
            background: #1e293b !important;
            color: #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 16px !important;
        }
        .gaks-service-account-info {
            margin-bottom: 20px;
            padding: 16px;
            background: #f0fdf4;
            border-radius: 8px;
            border: 1px solid #86efac;
        }
        .gaks-sa-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #166534;
            margin-bottom: 12px;
        }
        .gaks-sa-status .dashicons {
            color: #22c55e;
        }
        .gaks-sa-details {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .gaks-sa-detail {
            font-size: 13px;
        }
        .gaks-sa-detail .label {
            color: #64748b;
            margin-right: 8px;
        }
        .gaks-sa-detail .value {
            color: #1e293b;
            font-family: monospace;
        }
        .gaks-validation-result {
            margin-top: 8px;
        }
        .gaks-validation-result .success {
            color: #166534;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .gaks-validation-result .error {
            color: #991b1b;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        </style>
        <?php
    }
    
    /**
     * Register settings
     */
    public static function register_settings() {
        register_setting(
            'gaks_settings_group',
            'gaks_settings',
            array(
                'type' => 'array',
                'sanitize_callback' => array(__CLASS__, 'sanitize_settings')
            )
        );
    }
    
    /**
     * Sanitize settings
     */
    public static function sanitize_settings($input) {
        $sanitized = array();
        
        // Service account JSON - validate and store
        $json_input = $input['service_account_json'] ?? '';
        if (!empty($json_input)) {
            $validation = GAKS_API_Client::validate_service_account_json($json_input);
            if (is_wp_error($validation)) {
                add_settings_error('gaks_settings', 'invalid_json', $validation->get_error_message(), 'error');
                // Keep the old value if validation fails
                $old_settings = get_option('gaks_settings', array());
                $sanitized['service_account_json'] = $old_settings['service_account_json'] ?? '';
            } else {
                $sanitized['service_account_json'] = $json_input;
            }
        } else {
            $sanitized['service_account_json'] = '';
        }
        
        $sanitized['impersonate_email'] = sanitize_email($input['impersonate_email'] ?? '');
        $sanitized['developer_token'] = sanitize_text_field($input['developer_token'] ?? '');
        $sanitized['customer_id'] = preg_replace('/[^0-9]/', '', $input['customer_id'] ?? '');
        $sanitized['login_customer_id'] = preg_replace('/[^0-9]/', '', $input['login_customer_id'] ?? '');
        $sanitized['location_id'] = sanitize_text_field($input['location_id'] ?? '2840');
        $sanitized['language_id'] = sanitize_text_field($input['language_id'] ?? '1000');
        
        // Clear cached access token when settings change
        delete_transient('gaks_access_token');
        
        return $sanitized;
    }
    
    /**
     * Check if plugin is configured
     */
    public static function is_configured() {
        $settings = get_option('gaks_settings', array());
        
        return !empty($settings['service_account_json']) 
            && !empty($settings['developer_token']) 
            && !empty($settings['customer_id']);
    }
}

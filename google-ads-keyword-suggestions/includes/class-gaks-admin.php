<?php
/**
 * Admin settings page - OAuth 2.0 Version (works for everyone)
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
        ?>
        <div class="wrap gaks-settings-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="gaks-settings-container">
                <div class="gaks-settings-main">
                    <form method="post" action="options.php">
                        <?php settings_fields('gaks_settings_group'); ?>
                        
                        <!-- OAuth Credentials Card -->
                        <div class="gaks-card">
                            <h2 class="gaks-card-title">
                                <span class="dashicons dashicons-admin-network"></span>
                                <?php _e('OAuth 2.0 Credentials', 'google-ads-keyword-suggestions'); ?>
                            </h2>
                            
                            <table class="form-table gaks-form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="client_id"><?php _e('Client ID', 'google-ads-keyword-suggestions'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" 
                                               id="client_id" 
                                               name="gaks_settings[client_id]" 
                                               value="<?php echo esc_attr($settings['client_id'] ?? ''); ?>"
                                               class="large-text gaks-input"
                                               placeholder="xxxxxx.apps.googleusercontent.com"
                                               autocomplete="off">
                                        <p class="description"><?php _e('From Google Cloud Console → APIs & Services → Credentials', 'google-ads-keyword-suggestions'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="client_secret"><?php _e('Client Secret', 'google-ads-keyword-suggestions'); ?></label>
                                    </th>
                                    <td>
                                        <input type="password" 
                                               id="client_secret" 
                                               name="gaks_settings[client_secret]" 
                                               value="<?php echo esc_attr($settings['client_secret'] ?? ''); ?>"
                                               class="regular-text gaks-input"
                                               autocomplete="off">
                                        <p class="description"><?php _e('From the same OAuth 2.0 Client ID', 'google-ads-keyword-suggestions'); ?></p>
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
                                            <?php _e('(see Setup Guide for instructions)', 'google-ads-keyword-suggestions'); ?>
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
                                        <p class="description"><?php _e('Your Google Ads account ID (shown in top-right of Google Ads)', 'google-ads-keyword-suggestions'); ?></p>
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
                                        <p class="description"><?php _e('Only needed if using a Manager Account (MCC) to access another account', 'google-ads-keyword-suggestions'); ?></p>
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
                            <li><?php _e('Create Google Cloud project', 'google-ads-keyword-suggestions'); ?></li>
                            <li><?php _e('Enable Google Ads API', 'google-ads-keyword-suggestions'); ?></li>
                            <li><?php _e('Create OAuth credentials', 'google-ads-keyword-suggestions'); ?></li>
                            <li><?php _e('Get refresh token', 'google-ads-keyword-suggestions'); ?></li>
                            <li><?php _e('Get developer token', 'google-ads-keyword-suggestions'); ?></li>
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
        });
        </script>
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
        
        $sanitized['client_id'] = sanitize_text_field($input['client_id'] ?? '');
        $sanitized['client_secret'] = sanitize_text_field($input['client_secret'] ?? '');
        $sanitized['refresh_token'] = sanitize_text_field($input['refresh_token'] ?? '');
        $sanitized['developer_token'] = sanitize_text_field($input['developer_token'] ?? '');
        $sanitized['customer_id'] = preg_replace('/[^0-9]/', '', $input['customer_id'] ?? '');
        $sanitized['login_customer_id'] = preg_replace('/[^0-9]/', '', $input['login_customer_id'] ?? '');
        $sanitized['location_id'] = sanitize_text_field($input['location_id'] ?? '2840');
        $sanitized['language_id'] = sanitize_text_field($input['language_id'] ?? '1000');
        
        // Clear cached token when settings change
        delete_transient('gaks_access_token');
        
        return $sanitized;
    }
    
    /**
     * Check if plugin is configured
     */
    public static function is_configured() {
        $settings = get_option('gaks_settings', array());
        
        return !empty($settings['client_id']) 
            && !empty($settings['client_secret']) 
            && !empty($settings['refresh_token'])
            && !empty($settings['developer_token']) 
            && !empty($settings['customer_id']);
    }
}

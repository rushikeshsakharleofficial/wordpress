<?php
/**
 * Setup Guide page for the plugin - Service Account Version
 */

if (!defined('ABSPATH')) {
    exit;
}

class GAKS_Setup_Guide {
    
    /**
     * Render the setup guide page
     */
    public static function render_page() {
        $current_step = isset($_GET['step']) ? intval($_GET['step']) : 1;
        ?>
        <div class="wrap gaks-setup-wrap">
            <h1>
                <span class="dashicons dashicons-welcome-learn-more"></span>
                <?php _e('Keyword Suggestions Setup Guide', 'google-ads-keyword-suggestions'); ?>
            </h1>
            
            <div class="gaks-setup-container">
                <!-- Progress Steps -->
                <div class="gaks-progress-bar">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="gaks-progress-step <?php echo $i <= $current_step ? 'completed' : ''; ?> <?php echo $i === $current_step ? 'active' : ''; ?>">
                            <div class="step-number"><?php echo $i; ?></div>
                            <div class="step-label">
                                <?php
                                switch ($i) {
                                    case 1: _e('Service Account', 'google-ads-keyword-suggestions'); break;
                                    case 2: _e('Enable API', 'google-ads-keyword-suggestions'); break;
                                    case 3: _e('Developer Token', 'google-ads-keyword-suggestions'); break;
                                    case 4: _e('Configure Plugin', 'google-ads-keyword-suggestions'); break;
                                }
                                ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
                
                <!-- Step Content -->
                <div class="gaks-step-content">
                    <?php
                    switch ($current_step) {
                        case 1:
                            self::render_step_1();
                            break;
                        case 2:
                            self::render_step_2();
                            break;
                        case 3:
                            self::render_step_3();
                            break;
                        case 4:
                            self::render_step_4();
                            break;
                        default:
                            self::render_step_1();
                    }
                    ?>
                </div>
                
                <!-- Navigation -->
                <div class="gaks-step-nav">
                    <?php if ($current_step > 1): ?>
                        <a href="<?php echo admin_url('admin.php?page=gaks-setup-guide&step=' . ($current_step - 1)); ?>" class="button button-secondary">
                            ← <?php _e('Previous Step', 'google-ads-keyword-suggestions'); ?>
                        </a>
                    <?php else: ?>
                        <span></span>
                    <?php endif; ?>
                    
                    <?php if ($current_step < 4): ?>
                        <a href="<?php echo admin_url('admin.php?page=gaks-setup-guide&step=' . ($current_step + 1)); ?>" class="button button-primary">
                            <?php _e('Next Step', 'google-ads-keyword-suggestions'); ?> →
                        </a>
                    <?php else: ?>
                        <a href="<?php echo admin_url('options-general.php?page=gaks-settings'); ?>" class="button button-primary">
                            <?php _e('Go to Settings', 'google-ads-keyword-suggestions'); ?> →
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Step 1: Create GCP Service Account
     */
    private static function render_step_1() {
        ?>
        <div class="gaks-step-card">
            <h2>
                <span class="step-badge">Step 1</span>
                <?php _e('Create a GCP Service Account', 'google-ads-keyword-suggestions'); ?>
            </h2>
            
            <div class="gaks-info-box">
                <span class="dashicons dashicons-info-outline"></span>
                <p><?php _e('A Service Account allows your WordPress site to authenticate with Google Ads API without user interaction.', 'google-ads-keyword-suggestions'); ?></p>
            </div>
            
            <div class="gaks-instructions">
                <h3><?php _e('Instructions:', 'google-ads-keyword-suggestions'); ?></h3>
                
                <ol class="gaks-steps-list">
                    <li>
                        <strong><?php _e('Go to Google Cloud Console', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Open', 'google-ads-keyword-suggestions'); ?> <a href="https://console.cloud.google.com/" target="_blank">console.cloud.google.com</a> <?php _e('and sign in with your Google account.', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    
                    <li>
                        <strong><?php _e('Create or Select a Project', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Click the project dropdown at the top, then create a new project or select an existing one.', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    
                    <li>
                        <strong><?php _e('Go to Service Accounts', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Navigate to "IAM & Admin" → "Service Accounts" from the left menu.', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    
                    <li>
                        <strong><?php _e('Create Service Account', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Click "+ Create Service Account" at the top.', 'google-ads-keyword-suggestions'); ?></p>
                        <ul>
                            <li><?php _e('Name: "WordPress Keyword Suggestions"', 'google-ads-keyword-suggestions'); ?></li>
                            <li><?php _e('Description: "Service account for Google Ads API access"', 'google-ads-keyword-suggestions'); ?></li>
                        </ul>
                        <p><?php _e('Click "Create and Continue". You can skip the optional steps and click "Done".', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    
                    <li>
                        <strong><?php _e('Create JSON Key', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Click on the service account you just created, go to "Keys" tab, click "Add Key" → "Create new key" → select "JSON" → click "Create".', 'google-ads-keyword-suggestions'); ?></p>
                        <div class="gaks-warning">
                            <span class="dashicons dashicons-warning"></span>
                            <span><?php _e('Important: A JSON file will be downloaded. Keep this file secure - you\'ll need to paste its contents in the plugin settings!', 'google-ads-keyword-suggestions'); ?></span>
                        </div>
                    </li>
                    
                    <li>
                        <strong><?php _e('Note the Service Account Email', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Copy the service account email (looks like: name@project-id.iam.gserviceaccount.com). You\'ll need to add this to Google Ads.', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                </ol>
            </div>
            
            <div class="gaks-action-box">
                <a href="https://console.cloud.google.com/iam-admin/serviceaccounts/create" target="_blank" class="button button-hero">
                    <span class="dashicons dashicons-external"></span>
                    <?php _e('Create Service Account', 'google-ads-keyword-suggestions'); ?>
                </a>
            </div>
        </div>
        <?php
    }
    
    /**
     * Step 2: Enable Google Ads API
     */
    private static function render_step_2() {
        ?>
        <div class="gaks-step-card">
            <h2>
                <span class="step-badge">Step 2</span>
                <?php _e('Enable the Google Ads API', 'google-ads-keyword-suggestions'); ?>
            </h2>
            
            <div class="gaks-info-box">
                <span class="dashicons dashicons-info-outline"></span>
                <p><?php _e('You need to enable the Google Ads API for your project to access keyword data.', 'google-ads-keyword-suggestions'); ?></p>
            </div>
            
            <div class="gaks-instructions">
                <h3><?php _e('Instructions:', 'google-ads-keyword-suggestions'); ?></h3>
                
                <ol class="gaks-steps-list">
                    <li>
                        <strong><?php _e('Go to API Library', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('In Google Cloud Console, go to "APIs & Services" → "Library" from the left menu.', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    
                    <li>
                        <strong><?php _e('Search for Google Ads API', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Type "Google Ads API" in the search box and click on it when it appears.', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    
                    <li>
                        <strong><?php _e('Enable the API', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Click the blue "Enable" button on the Google Ads API page.', 'google-ads-keyword-suggestions'); ?></p>
                        <div class="gaks-warning">
                            <span class="dashicons dashicons-warning"></span>
                            <span><?php _e('Important: Make sure you\'re enabling "Google Ads API", not "Google Ads Data Hub API" or similar.', 'google-ads-keyword-suggestions'); ?></span>
                        </div>
                    </li>
                    
                    <li>
                        <strong><?php _e('Grant Service Account Access in Google Ads', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Go to your Google Ads account → Admin → Access and security → Add the service account email with "Standard" access.', 'google-ads-keyword-suggestions'); ?></p>
                        <div class="gaks-tip">
                            <span class="dashicons dashicons-lightbulb"></span>
                            <span><?php _e('Tip: The service account email looks like: name@project-id.iam.gserviceaccount.com', 'google-ads-keyword-suggestions'); ?></span>
                        </div>
                    </li>
                </ol>
            </div>
            
            <div class="gaks-action-box">
                <a href="https://console.cloud.google.com/apis/library/googleads.googleapis.com" target="_blank" class="button button-hero">
                    <span class="dashicons dashicons-external"></span>
                    <?php _e('Enable Google Ads API', 'google-ads-keyword-suggestions'); ?>
                </a>
            </div>
        </div>
        <?php
    }
    
    /**
     * Step 3: Get Developer Token
     */
    private static function render_step_3() {
        ?>
        <div class="gaks-step-card">
            <h2>
                <span class="step-badge">Step 3</span>
                <?php _e('Get Your Developer Token', 'google-ads-keyword-suggestions'); ?>
            </h2>
            
            <div class="gaks-info-box warning">
                <span class="dashicons dashicons-warning"></span>
                <p><?php _e('You need a Google Ads Manager Account (MCC) to get a developer token. If you don\'t have one, you\'ll need to create it first.', 'google-ads-keyword-suggestions'); ?></p>
            </div>
            
            <div class="gaks-instructions">
                <h3><?php _e('Instructions:', 'google-ads-keyword-suggestions'); ?></h3>
                
                <ol class="gaks-steps-list">
                    <li>
                        <strong><?php _e('Access Google Ads', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Go to', 'google-ads-keyword-suggestions'); ?> <a href="https://ads.google.com" target="_blank">ads.google.com</a> <?php _e('and sign in.', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    
                    <li>
                        <strong><?php _e('Create Manager Account (if needed)', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('If you don\'t have a Manager Account:', 'google-ads-keyword-suggestions'); ?></p>
                        <ul>
                            <li><?php _e('Go to', 'google-ads-keyword-suggestions'); ?> <a href="https://ads.google.com/home/tools/manager-accounts/" target="_blank"><?php _e('Manager Accounts', 'google-ads-keyword-suggestions'); ?></a></li>
                            <li><?php _e('Click "Create a manager account"', 'google-ads-keyword-suggestions'); ?></li>
                            <li><?php _e('Follow the setup wizard', 'google-ads-keyword-suggestions'); ?></li>
                        </ul>
                    </li>
                    
                    <li>
                        <strong><?php _e('Go to API Center', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('In your Manager Account, click "Tools & Settings" (wrench icon) → "API Center" under "Setup".', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    
                    <li>
                        <strong><?php _e('Apply for Developer Token', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('If you don\'t have a token yet, apply for "Basic Access". This is usually approved within a few days.', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    
                    <li>
                        <strong><?php _e('Copy Your Credentials', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('You\'ll need:', 'google-ads-keyword-suggestions'); ?></p>
                        <ul>
                            <li><strong><?php _e('Developer Token', 'google-ads-keyword-suggestions'); ?></strong> - <?php _e('From API Center', 'google-ads-keyword-suggestions'); ?></li>
                            <li><strong><?php _e('Customer ID', 'google-ads-keyword-suggestions'); ?></strong> - <?php _e('Displayed in top right (XXX-XXX-XXXX)', 'google-ads-keyword-suggestions'); ?></li>
                            <li><strong><?php _e('Login Customer ID', 'google-ads-keyword-suggestions'); ?></strong> - <?php _e('Manager account ID (if using MCC)', 'google-ads-keyword-suggestions'); ?></li>
                        </ul>
                    </li>
                </ol>
            </div>
            
            <div class="gaks-action-box">
                <a href="https://ads.google.com" target="_blank" class="button button-hero">
                    <span class="dashicons dashicons-external"></span>
                    <?php _e('Open Google Ads', 'google-ads-keyword-suggestions'); ?>
                </a>
            </div>
        </div>
        <?php
    }
    
    /**
     * Step 4: Configure Plugin
     */
    private static function render_step_4() {
        $settings = get_option('gaks_settings', array());
        $is_configured = GAKS_Admin::is_configured();
        ?>
        <div class="gaks-step-card">
            <h2>
                <span class="step-badge">Step 4</span>
                <?php _e('Configure the Plugin', 'google-ads-keyword-suggestions'); ?>
            </h2>
            
            <?php if ($is_configured): ?>
                <div class="gaks-info-box success">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <p><?php _e('Great news! Your plugin is already configured. You can start using keyword suggestions!', 'google-ads-keyword-suggestions'); ?></p>
                </div>
            <?php else: ?>
                <div class="gaks-info-box">
                    <span class="dashicons dashicons-info-outline"></span>
                    <p><?php _e('Now paste your service account JSON and credentials into the plugin settings.', 'google-ads-keyword-suggestions'); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="gaks-instructions">
                <h3><?php _e('Credentials Checklist:', 'google-ads-keyword-suggestions'); ?></h3>
                
                <div class="gaks-checklist">
                    <label class="gaks-check-item <?php echo !empty($settings['service_account_json']) ? 'checked' : ''; ?>">
                        <span class="dashicons <?php echo !empty($settings['service_account_json']) ? 'dashicons-yes' : 'dashicons-marker'; ?>"></span>
                        <?php _e('Service Account JSON', 'google-ads-keyword-suggestions'); ?>
                    </label>
                    <label class="gaks-check-item <?php echo !empty($settings['developer_token']) ? 'checked' : ''; ?>">
                        <span class="dashicons <?php echo !empty($settings['developer_token']) ? 'dashicons-yes' : 'dashicons-marker'; ?>"></span>
                        <?php _e('Developer Token', 'google-ads-keyword-suggestions'); ?>
                    </label>
                    <label class="gaks-check-item <?php echo !empty($settings['customer_id']) ? 'checked' : ''; ?>">
                        <span class="dashicons <?php echo !empty($settings['customer_id']) ? 'dashicons-yes' : 'dashicons-marker'; ?>"></span>
                        <?php _e('Customer ID', 'google-ads-keyword-suggestions'); ?>
                    </label>
                </div>
                
                <h3><?php _e('Final Steps:', 'google-ads-keyword-suggestions'); ?></h3>
                
                <ol class="gaks-steps-list">
                    <li>
                        <strong><?php _e('Paste Service Account JSON', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Open the downloaded JSON key file, copy ALL its contents, and paste into the Service Account JSON field.', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    
                    <li>
                        <strong><?php _e('Enter Developer Token & Customer ID', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Enter your Developer Token and Customer ID from Google Ads.', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    
                    <li>
                        <strong><?php _e('Test Connection', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Click the "Test Connection" button to verify everything works.', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    
                    <li>
                        <strong><?php _e('Start Using!', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Create or edit a post, then open the Keyword Suggestions panel in the sidebar to get keyword ideas!', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                </ol>
            </div>
            
            <div class="gaks-action-box">
                <a href="<?php echo admin_url('options-general.php?page=gaks-settings'); ?>" class="button button-hero button-primary">
                    <span class="dashicons dashicons-admin-settings"></span>
                    <?php _e('Go to Plugin Settings', 'google-ads-keyword-suggestions'); ?>
                </a>
            </div>
            
            <?php if ($is_configured): ?>
                <div class="gaks-success-box">
                    <h3>🎉 <?php _e('You\'re all set!', 'google-ads-keyword-suggestions'); ?></h3>
                    <p><?php _e('Open any post in the block editor and click the Keyword Suggestions icon in the sidebar to start finding great keywords for your content!', 'google-ads-keyword-suggestions'); ?></p>
                    <a href="<?php echo admin_url('post-new.php'); ?>" class="button button-primary">
                        <?php _e('Create New Post', 'google-ads-keyword-suggestions'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}

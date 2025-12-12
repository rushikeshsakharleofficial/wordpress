<?php
/**
 * Setup Guide page - OAuth 2.0 Version (works for everyone)
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
        $max_steps = 5;
        ?>
        <div class="wrap gaks-setup-wrap">
            <h1>
                <span class="dashicons dashicons-welcome-learn-more"></span>
                <?php _e('Keyword Suggestions Setup Guide', 'google-ads-keyword-suggestions'); ?>
            </h1>
            
            <div class="gaks-setup-container">
                <!-- Progress Steps -->
                <div class="gaks-progress-bar">
                    <?php for ($i = 1; $i <= $max_steps; $i++): ?>
                        <div class="gaks-progress-step <?php echo $i <= $current_step ? 'completed' : ''; ?> <?php echo $i === $current_step ? 'active' : ''; ?>">
                            <div class="step-number"><?php echo $i; ?></div>
                            <div class="step-label">
                                <?php
                                switch ($i) {
                                    case 1: _e('Cloud Project', 'google-ads-keyword-suggestions'); break;
                                    case 2: _e('Enable API', 'google-ads-keyword-suggestions'); break;
                                    case 3: _e('OAuth Setup', 'google-ads-keyword-suggestions'); break;
                                    case 4: _e('Dev Token', 'google-ads-keyword-suggestions'); break;
                                    case 5: _e('Configure', 'google-ads-keyword-suggestions'); break;
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
                        case 1: self::render_step_1(); break;
                        case 2: self::render_step_2(); break;
                        case 3: self::render_step_3(); break;
                        case 4: self::render_step_4(); break;
                        case 5: self::render_step_5(); break;
                        default: self::render_step_1();
                    }
                    ?>
                </div>
                
                <!-- Navigation -->
                <div class="gaks-step-nav">
                    <?php if ($current_step > 1): ?>
                        <a href="<?php echo admin_url('admin.php?page=gaks-setup-guide&step=' . ($current_step - 1)); ?>" class="button button-secondary">
                            ← <?php _e('Previous', 'google-ads-keyword-suggestions'); ?>
                        </a>
                    <?php else: ?>
                        <span></span>
                    <?php endif; ?>
                    
                    <?php if ($current_step < $max_steps): ?>
                        <a href="<?php echo admin_url('admin.php?page=gaks-setup-guide&step=' . ($current_step + 1)); ?>" class="button button-primary">
                            <?php _e('Next', 'google-ads-keyword-suggestions'); ?> →
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
     * Step 1: Create Google Cloud Project
     */
    private static function render_step_1() {
        ?>
        <div class="gaks-step-card">
            <h2><span class="step-badge">Step 1</span> <?php _e('Create Google Cloud Project', 'google-ads-keyword-suggestions'); ?></h2>
            
            <div class="gaks-info-box">
                <span class="dashicons dashicons-info-outline"></span>
                <p><?php _e('A free Google Cloud project is required to use the Google Ads API.', 'google-ads-keyword-suggestions'); ?></p>
            </div>
            
            <div class="gaks-instructions">
                <ol class="gaks-steps-list">
                    <li>
                        <strong><?php _e('Go to Google Cloud Console', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><a href="https://console.cloud.google.com/" target="_blank">console.cloud.google.com</a> - <?php _e('Sign in with your Google account', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    <li>
                        <strong><?php _e('Create New Project', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Click project dropdown → "New Project" → Name it "WordPress Keywords" → Create', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                </ol>
            </div>
            
            <div class="gaks-action-box">
                <a href="https://console.cloud.google.com/projectcreate" target="_blank" class="button button-hero button-primary">
                    <?php _e('Create Project', 'google-ads-keyword-suggestions'); ?> →
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
            <h2><span class="step-badge">Step 2</span> <?php _e('Enable Google Ads API', 'google-ads-keyword-suggestions'); ?></h2>
            
            <div class="gaks-instructions">
                <ol class="gaks-steps-list">
                    <li>
                        <strong><?php _e('Open API Library', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Go to APIs & Services → Library', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    <li>
                        <strong><?php _e('Search & Enable', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Search "Google Ads API" → Click it → Click "Enable"', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                </ol>
            </div>
            
            <div class="gaks-action-box">
                <a href="https://console.cloud.google.com/apis/library/googleads.googleapis.com" target="_blank" class="button button-hero button-primary">
                    <?php _e('Enable Google Ads API', 'google-ads-keyword-suggestions'); ?> →
                </a>
            </div>
        </div>
        <?php
    }
    
    /**
     * Step 3: Create OAuth Credentials & Get Refresh Token
     */
    private static function render_step_3() {
        ?>
        <div class="gaks-step-card">
            <h2><span class="step-badge">Step 3</span> <?php _e('Create OAuth Credentials', 'google-ads-keyword-suggestions'); ?></h2>
            
            <div class="gaks-info-box">
                <span class="dashicons dashicons-info-outline"></span>
                <p><?php _e('This step creates credentials that allow the plugin to access Google Ads on your behalf.', 'google-ads-keyword-suggestions'); ?></p>
            </div>
            
            <div class="gaks-instructions">
                <h3><?php _e('Part A: Configure Consent Screen', 'google-ads-keyword-suggestions'); ?></h3>
                <ol class="gaks-steps-list">
                    <li>
                        <strong><?php _e('OAuth Consent Screen', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Go to APIs & Services → OAuth consent screen → Select "External" → Create', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    <li>
                        <strong><?php _e('Fill Required Fields', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('App name, User support email, Developer email → Save and Continue through all screens', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                </ol>
                
                <h3><?php _e('Part B: Create OAuth Client ID', 'google-ads-keyword-suggestions'); ?></h3>
                <ol class="gaks-steps-list" start="3">
                    <li>
                        <strong><?php _e('Create Credentials', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Go to Credentials → + Create Credentials → OAuth client ID', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    <li>
                        <strong><?php _e('Select Desktop App', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Application type: Desktop app → Name: "WordPress Plugin" → Create', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    <li>
                        <strong><?php _e('Copy Credentials', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><strong><?php _e('Save your Client ID and Client Secret!', 'google-ads-keyword-suggestions'); ?></strong></p>
                    </li>
                </ol>
                
                <h3><?php _e('Part C: Get Refresh Token (Important!)', 'google-ads-keyword-suggestions'); ?></h3>
                <ol class="gaks-steps-list" start="6">
                    <li>
                        <strong><?php _e('Open OAuth Playground', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><a href="https://developers.google.com/oauthplayground/" target="_blank">developers.google.com/oauthplayground</a></p>
                    </li>
                    <li>
                        <strong><?php _e('Configure Settings (Gear Icon ⚙️)', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Check "Use your own OAuth credentials" → Enter your Client ID and Client Secret', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    <li>
                        <strong><?php _e('Select Google Ads API', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('In left panel, find "Google Ads API v17" → Check the adwords scope → Click "Authorize APIs"', 'google-ads-keyword-suggestions'); ?></p>
                        <code class="gaks-code-block">https://www.googleapis.com/auth/adwords</code>
                    </li>
                    <li>
                        <strong><?php _e('Get Tokens', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Sign in with your Google account → Click "Exchange authorization code for tokens" → Copy the Refresh Token', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                </ol>
            </div>
            
            <div class="gaks-action-box">
                <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="button button-hero button-primary">
                    <?php _e('Create Credentials', 'google-ads-keyword-suggestions'); ?> →
                </a>
                <a href="https://developers.google.com/oauthplayground/" target="_blank" class="button button-hero button-secondary">
                    <?php _e('OAuth Playground', 'google-ads-keyword-suggestions'); ?> →
                </a>
            </div>
        </div>
        <?php
    }
    
    /**
     * Step 4: Get Developer Token
     */
    private static function render_step_4() {
        ?>
        <div class="gaks-step-card">
            <h2><span class="step-badge">Step 4</span> <?php _e('Get Developer Token', 'google-ads-keyword-suggestions'); ?></h2>
            
            <div class="gaks-info-box warning">
                <span class="dashicons dashicons-warning"></span>
                <p><?php _e('You need a Google Ads Manager Account (free to create) to get a developer token.', 'google-ads-keyword-suggestions'); ?></p>
            </div>
            
            <div class="gaks-instructions">
                <ol class="gaks-steps-list">
                    <li>
                        <strong><?php _e('Create Manager Account (if needed)', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><a href="https://ads.google.com/home/tools/manager-accounts/" target="_blank"><?php _e('Create Manager Account', 'google-ads-keyword-suggestions'); ?></a> - <?php _e('Free and takes 2 minutes', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    <li>
                        <strong><?php _e('Go to API Center', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('In Manager Account: Tools & Settings (🔧) → Setup → API Center', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    <li>
                        <strong><?php _e('Get or Apply for Token', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Copy your Developer Token, or apply for Basic Access (approved in 1-3 days)', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                    <li>
                        <strong><?php _e('Note Your Customer ID', 'google-ads-keyword-suggestions'); ?></strong>
                        <p><?php _e('Top-right corner shows your ID (format: XXX-XXX-XXXX)', 'google-ads-keyword-suggestions'); ?></p>
                    </li>
                </ol>
            </div>
            
            <div class="gaks-action-box">
                <a href="https://ads.google.com" target="_blank" class="button button-hero button-primary">
                    <?php _e('Open Google Ads', 'google-ads-keyword-suggestions'); ?> →
                </a>
            </div>
        </div>
        <?php
    }
    
    /**
     * Step 5: Configure Plugin
     */
    private static function render_step_5() {
        $is_configured = GAKS_Admin::is_configured();
        ?>
        <div class="gaks-step-card">
            <h2><span class="step-badge">Step 5</span> <?php _e('Configure Plugin', 'google-ads-keyword-suggestions'); ?></h2>
            
            <?php if ($is_configured): ?>
                <div class="gaks-info-box success">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <p><?php _e('Your plugin is configured! You can start using keyword suggestions.', 'google-ads-keyword-suggestions'); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="gaks-instructions">
                <h3><?php _e('Enter These in Settings:', 'google-ads-keyword-suggestions'); ?></h3>
                <ul style="list-style: disc; padding-left: 20px; margin: 16px 0;">
                    <li><strong><?php _e('Client ID', 'google-ads-keyword-suggestions'); ?></strong> - <?php _e('From Step 3', 'google-ads-keyword-suggestions'); ?></li>
                    <li><strong><?php _e('Client Secret', 'google-ads-keyword-suggestions'); ?></strong> - <?php _e('From Step 3', 'google-ads-keyword-suggestions'); ?></li>
                    <li><strong><?php _e('Refresh Token', 'google-ads-keyword-suggestions'); ?></strong> - <?php _e('From OAuth Playground', 'google-ads-keyword-suggestions'); ?></li>
                    <li><strong><?php _e('Developer Token', 'google-ads-keyword-suggestions'); ?></strong> - <?php _e('From Google Ads API Center', 'google-ads-keyword-suggestions'); ?></li>
                    <li><strong><?php _e('Customer ID', 'google-ads-keyword-suggestions'); ?></strong> - <?php _e('Your Google Ads account ID', 'google-ads-keyword-suggestions'); ?></li>
                </ul>
            </div>
            
            <div class="gaks-action-box">
                <a href="<?php echo admin_url('options-general.php?page=gaks-settings'); ?>" class="button button-hero button-primary">
                    <?php _e('Go to Settings', 'google-ads-keyword-suggestions'); ?> →
                </a>
            </div>
            
            <?php if ($is_configured): ?>
                <div class="gaks-success-box">
                    <h3>🎉 <?php _e('Ready to use!', 'google-ads-keyword-suggestions'); ?></h3>
                    <p><?php _e('Open any post and use the Keyword Suggestions sidebar.', 'google-ads-keyword-suggestions'); ?></p>
                    <a href="<?php echo admin_url('post-new.php'); ?>" class="button button-primary">
                        <?php _e('Create New Post', 'google-ads-keyword-suggestions'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}

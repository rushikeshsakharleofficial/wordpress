# Google Ads Keyword Suggestions

A WordPress plugin that integrates with the Google Ads API to provide real-time keyword suggestions while writing blog posts, helping content creators optimize their SEO strategy.

**Version:** 1.0.3  
**Author:** Rushikesh Sakharle

## Features

- 🔍 **Real-time Keyword Suggestions** - Get keyword ideas from Google Ads Keyword Planner directly in the WordPress editor
- 📊 **Keyword Metrics** - View monthly search volume, competition level, and estimated CPC for each keyword
- ✏️ **One-Click Insert** - Insert keywords into your content or add them as tags with a single click
- 🎯 **Geographic Targeting** - Configure target location and language for localized keyword suggestions
- 🔐 **Service Account Auth** - Uses GCP Service Account JSON for secure, automated authentication

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- PHP OpenSSL extension (for JWT signing)
- Google Ads Manager Account with approved Developer Token
- GCP Service Account with Google Ads API enabled

## Installation

1. Download the plugin ZIP file or clone this repository
2. Upload to `/wp-content/plugins/google-ads-keyword-suggestions/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to **Keywords Setup** menu for step-by-step configuration guide

## Configuration

### Quick Setup

1. **Create GCP Service Account**
   - Go to [Google Cloud Console](https://console.cloud.google.com/iam-admin/serviceaccounts)
   - Create a new service account
   - Create and download a JSON key

2. **Enable Google Ads API**
   - Enable the [Google Ads API](https://console.cloud.google.com/apis/library/googleads.googleapis.com) for your project

3. **Get Developer Token**
   - Get your developer token from [Google Ads API Center](https://ads.google.com)

4. **Grant Access**
   - Add the service account email to your Google Ads account with standard access

5. **Configure Plugin**
   - Go to **Settings → Keyword Suggestions**
   - Paste the JSON key file contents
   - Enter Developer Token and Customer ID
   - Click "Test Connection"

### Plugin Settings

Navigate to **Settings → Keyword Suggestions** and enter:

- **Service Account JSON** - Contents of your downloaded JSON key file
- **Developer Token** - From Google Ads API Center
- **Customer ID** - Your Google Ads account number
- **Login Customer ID** - Manager account ID (if applicable)
- **Target Location** - Geographic location for suggestions
- **Target Language** - Language for keyword suggestions

## Usage

1. Create or edit a post/page in WordPress
2. Click the puzzle piece icon in the top toolbar to open the Keyword Suggestions sidebar
3. Enter seed keywords (comma or newline separated)
4. Click **Get Suggestions**
5. Browse the keyword suggestions with their metrics
6. Click **Insert** to add a keyword to your content
7. Click **Add Tag** to add a keyword as a post tag

## Troubleshooting

### "Invalid private key" error
- Ensure you're pasting the COMPLETE JSON file contents including curly braces
- The JSON must be a service account key (type: "service_account")

### "Connection failed" error
- Verify Developer Token is approved and active
- Check Customer ID is correct
- Ensure service account email is added to Google Ads with proper access

### No suggestions returned
- Try different seed keywords
- Check your Google Ads account has Keyword Planner access
- Verify API quota is not exceeded

## Security

- Service account JSON is stored encrypted in WordPress database
- Access tokens are cached using WordPress transients (50 min expiry)
- REST endpoints require `edit_posts` capability
- All inputs are sanitized and validated

## License

GPL v2 or later

## Changelog

### 1.0.3
- Switched to GCP Service Account authentication (simpler setup)
- Updated setup guide with 4-step wizard
- Added JSON validation
- Added impersonate email option

### 1.0.0
- Initial release

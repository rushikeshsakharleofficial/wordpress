# Google Ads Keyword Suggestions

A WordPress plugin that integrates with the Google Ads API to provide real-time keyword suggestions while writing blog posts.

**Version:** 1.0.3  
**Author:** Rushikesh Sakharle

## Features

- 🔍 **Real-time Keyword Suggestions** - Get keyword ideas from Google Ads Keyword Planner
- 📊 **Keyword Metrics** - View monthly search volume, competition level, and CPC
- ✏️ **One-Click Insert** - Add keywords to content or as post tags
- 🎯 **Geographic Targeting** - Configure location and language
- ✅ **Works for Everyone** - Personal Gmail accounts or organization accounts

## Requirements

- WordPress 5.8+
- PHP 7.4+
- Google Ads account with Keyword Planner access
- Google Ads Developer Token (free)

## Quick Setup

1. **Create Google Cloud Project** → [console.cloud.google.com](https://console.cloud.google.com)
2. **Enable Google Ads API**
3. **Create OAuth Credentials** (Desktop app type)
4. **Get Refresh Token** from [OAuth Playground](https://developers.google.com/oauthplayground/)
5. **Get Developer Token** from Google Ads → Tools → API Center
6. **Enter credentials** in Settings → Keyword Suggestions

See the in-plugin **Setup Guide** for detailed step-by-step instructions.

## Installation

1. Upload `google-ads-keyword-suggestions` folder to `/wp-content/plugins/`
2. Activate the plugin
3. Go to **Keywords Setup** menu for guided setup
4. Configure credentials in **Settings → Keyword Suggestions**

## Settings Required

| Setting | Where to Get It |
|---------|-----------------|
| Client ID | Google Cloud Console → Credentials |
| Client Secret | Google Cloud Console → Credentials |
| Refresh Token | OAuth Playground |
| Developer Token | Google Ads → API Center |
| Customer ID | Top-right of Google Ads (XXX-XXX-XXXX) |

## Usage

1. Edit any post in WordPress
2. Open **Keyword Suggestions** sidebar panel
3. Enter seed keywords
4. Click **Get Suggestions**
5. Insert keywords into content or add as tags

## Changelog

### 1.0.3
- OAuth 2.0 authentication (works for everyone)
- Simplified 5-step setup guide
- Updated admin UI

### 1.0.0
- Initial release

## License

GPL v2 or later

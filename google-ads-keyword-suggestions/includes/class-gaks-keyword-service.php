<?php
/**
 * Keyword suggestion service using Google Ads KeywordPlanIdeaService
 */

if (!defined('ABSPATH')) {
    exit;
}

class GAKS_Keyword_Service {
    
    /**
     * API Client instance
     */
    private $api_client;
    
    /**
     * Plugin settings
     */
    private $settings;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->api_client = new GAKS_API_Client();
        $this->settings = get_option('gaks_settings', array());
    }
    
    /**
     * Generate keyword ideas from seed keywords
     */
    public function generate_keyword_ideas($seed_keywords) {
        // Parse keywords (comma or newline separated)
        $keywords = array_filter(array_map('trim', preg_split('/[,\n]/', $seed_keywords)));
        
        if (empty($keywords)) {
            return new WP_Error('no_keywords', __('Please provide at least one keyword.', 'google-ads-keyword-suggestions'));
        }
        
        // Limit to 10 seed keywords
        $keywords = array_slice($keywords, 0, 10);
        
        $location_id = $this->settings['location_id'] ?? '2840';
        $language_id = $this->settings['language_id'] ?? '1000';
        
        // Build the request body for generateKeywordIdeas
        $request_body = array(
            'keywordSeed' => array(
                'keywords' => $keywords
            ),
            'geoTargetConstants' => array(
                sprintf('geoTargetConstants/%s', $location_id)
            ),
            'language' => sprintf('languageConstants/%s', $language_id),
            'keywordPlanNetwork' => 'GOOGLE_SEARCH',
            'pageSize' => 50
        );
        
        $response = $this->api_client->request(
            'googleAds:generateKeywordIdeas',
            'POST',
            $request_body
        );
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        return $this->format_suggestions($response);
    }
    
    /**
     * Format API response into usable suggestions
     */
    private function format_suggestions($response) {
        $suggestions = array();
        
        if (!isset($response['results']) || !is_array($response['results'])) {
            return $suggestions;
        }
        
        foreach ($response['results'] as $result) {
            $keyword = $result['text'] ?? '';
            
            if (empty($keyword)) {
                continue;
            }
            
            $metrics = $result['keywordIdeaMetrics'] ?? array();
            
            // Parse monthly searches
            $avg_monthly_searches = 0;
            if (isset($metrics['avgMonthlySearches'])) {
                $avg_monthly_searches = intval($metrics['avgMonthlySearches']);
            }
            
            // Parse competition level
            $competition = 'UNKNOWN';
            if (isset($metrics['competition'])) {
                $competition = $metrics['competition'];
            }
            
            // Competition index (0-100)
            $competition_index = $metrics['competitionIndex'] ?? 0;
            
            // Low and high range for top of page bid
            $low_bid = $metrics['lowTopOfPageBidMicros'] ?? 0;
            $high_bid = $metrics['highTopOfPageBidMicros'] ?? 0;
            
            $suggestions[] = array(
                'keyword' => $keyword,
                'monthly_searches' => $avg_monthly_searches,
                'monthly_searches_formatted' => $this->format_number($avg_monthly_searches),
                'competition' => $competition,
                'competition_label' => $this->get_competition_label($competition),
                'competition_index' => intval($competition_index),
                'low_bid' => $this->format_micros($low_bid),
                'high_bid' => $this->format_micros($high_bid)
            );
        }
        
        // Sort by monthly searches (descending)
        usort($suggestions, function($a, $b) {
            return $b['monthly_searches'] - $a['monthly_searches'];
        });
        
        return $suggestions;
    }
    
    /**
     * Format number for display
     */
    private function format_number($number) {
        if ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        }
        return number_format($number);
    }
    
    /**
     * Format micros to currency
     */
    private function format_micros($micros) {
        if (empty($micros)) {
            return '-';
        }
        return '$' . number_format($micros / 1000000, 2);
    }
    
    /**
     * Get human-readable competition label
     */
    private function get_competition_label($competition) {
        $labels = array(
            'UNSPECIFIED' => __('Unknown', 'google-ads-keyword-suggestions'),
            'UNKNOWN' => __('Unknown', 'google-ads-keyword-suggestions'),
            'LOW' => __('Low', 'google-ads-keyword-suggestions'),
            'MEDIUM' => __('Medium', 'google-ads-keyword-suggestions'),
            'HIGH' => __('High', 'google-ads-keyword-suggestions')
        );
        
        return $labels[$competition] ?? __('Unknown', 'google-ads-keyword-suggestions');
    }
}

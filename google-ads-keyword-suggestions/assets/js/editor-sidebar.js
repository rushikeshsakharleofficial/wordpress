/**
 * Google Ads Keyword Suggestions - Gutenberg Sidebar Panel
 */

(function(wp) {
    const { registerPlugin } = wp.plugins;
    const { PluginSidebar, PluginSidebarMoreMenuItem } = wp.editPost;
    const { useState, useEffect } = wp.element;
    const { TextareaControl, Button, Spinner, Notice, PanelBody, Icon } = wp.components;
    const { dispatch, select } = wp.data;
    const apiFetch = wp.apiFetch;
    
    // Keyword icon
    const KeywordIcon = () => (
        wp.element.createElement('svg', {
            width: 24,
            height: 24,
            viewBox: '0 0 24 24',
            fill: 'none',
            xmlns: 'http://www.w3.org/2000/svg'
        },
            wp.element.createElement('path', {
                d: 'M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z',
                fill: 'currentColor'
            })
        )
    );
    
    // Competition badge component
    const CompetitionBadge = ({ level }) => {
        const colors = {
            'Low': '#22c55e',
            'Medium': '#f59e0b',
            'High': '#ef4444',
            'Unknown': '#9ca3af'
        };
        
        return wp.element.createElement('span', {
            className: 'gaks-competition-badge',
            style: {
                backgroundColor: colors[level] || colors['Unknown'],
                color: '#fff',
                padding: '2px 8px',
                borderRadius: '12px',
                fontSize: '11px',
                fontWeight: '500'
            }
        }, level);
    };
    
    // Keyword card component
    const KeywordCard = ({ keyword, onInsert }) => {
        return wp.element.createElement('div', {
            className: 'gaks-keyword-card'
        },
            wp.element.createElement('div', { className: 'gaks-keyword-header' },
                wp.element.createElement('span', { className: 'gaks-keyword-text' }, keyword.keyword),
                wp.element.createElement(CompetitionBadge, { level: keyword.competition_label })
            ),
            wp.element.createElement('div', { className: 'gaks-keyword-metrics' },
                wp.element.createElement('div', { className: 'gaks-metric' },
                    wp.element.createElement('span', { className: 'gaks-metric-value' }, keyword.monthly_searches_formatted),
                    wp.element.createElement('span', { className: 'gaks-metric-label' }, 'Monthly Searches')
                ),
                wp.element.createElement('div', { className: 'gaks-metric' },
                    wp.element.createElement('span', { className: 'gaks-metric-value' }, keyword.high_bid),
                    wp.element.createElement('span', { className: 'gaks-metric-label' }, 'CPC (High)')
                )
            ),
            wp.element.createElement('div', { className: 'gaks-keyword-actions' },
                wp.element.createElement(Button, {
                    isSecondary: true,
                    isSmall: true,
                    onClick: () => onInsert(keyword.keyword, 'content'),
                    title: 'Insert into content'
                }, 'Insert'),
                wp.element.createElement(Button, {
                    isSecondary: true,
                    isSmall: true,
                    onClick: () => onInsert(keyword.keyword, 'tag'),
                    title: 'Add as tag'
                }, 'Add Tag')
            )
        );
    };
    
    // Main sidebar component
    const KeywordSuggestionsSidebar = () => {
        const [keywords, setKeywords] = useState('');
        const [suggestions, setSuggestions] = useState([]);
        const [loading, setLoading] = useState(false);
        const [error, setError] = useState(null);
        const [successMessage, setSuccessMessage] = useState(null);
        
        // Auto-extract keywords from title
        useEffect(() => {
            const title = select('core/editor').getEditedPostAttribute('title');
            if (title && !keywords) {
                // Extract words longer than 3 characters as seed keywords
                const words = title.split(/\s+/).filter(word => word.length > 3);
                if (words.length > 0) {
                    setKeywords(words.slice(0, 3).join(', '));
                }
            }
        }, []);
        
        // Fetch suggestions from API
        const fetchSuggestions = async () => {
            if (!keywords.trim()) {
                setError('Please enter at least one keyword.');
                return;
            }
            
            setLoading(true);
            setError(null);
            setSuggestions([]);
            
            try {
                const response = await apiFetch({
                    path: '/gaks/v1/suggestions',
                    method: 'POST',
                    data: { keywords: keywords }
                });
                
                if (Array.isArray(response)) {
                    setSuggestions(response);
                    if (response.length === 0) {
                        setError('No suggestions found for these keywords.');
                    }
                } else {
                    setError('Unexpected response from server.');
                }
            } catch (err) {
                setError(err.message || 'Failed to fetch suggestions.');
            } finally {
                setLoading(false);
            }
        };
        
        // Insert keyword into content or as tag
        const insertKeyword = async (keyword, type) => {
            setSuccessMessage(null);
            
            if (type === 'content') {
                // Insert at cursor position in the editor
                dispatch('core/block-editor').insertBlocks(
                    wp.blocks.createBlock('core/paragraph', {
                        content: keyword
                    })
                );
                setSuccessMessage(`Added "${keyword}" to content`);
            } else if (type === 'tag') {
                // Add to post tags
                const currentTags = select('core/editor').getEditedPostAttribute('tags') || [];
                
                // Create tagalog if it doesn't exist
                try {
                    const response = await apiFetch({
                        path: '/wp/v2/tags',
                        method: 'POST',
                        data: { name: keyword }
                    });
                    
                    if (response && response.id) {
                        dispatch('core/editor').editPost({
                            tags: [...currentTags, response.id]
                        });
                        setSuccessMessage(`Added "${keyword}" as tag`);
                    }
                } catch (err) {
                    // Tag might already exist, try to find it
                    try {
                        const existingTags = await apiFetch({
                            path: `/wp/v2/tags?search=${encodeURIComponent(keyword)}`
                        });
                        
                        if (existingTags && existingTags.length > 0) {
                            const matchingTag = existingTags.find(t => 
                                t.name.toLowerCase() === keyword.toLowerCase()
                            );
                            
                            if (matchingTag && !currentTags.includes(matchingTag.id)) {
                                dispatch('core/editor').editPost({
                                    tags: [...currentTags, matchingTag.id]
                                });
                                setSuccessMessage(`Added "${keyword}" as tag`);
                            } else {
                                setError('Tag already added to this post.');
                            }
                        }
                    } catch (searchErr) {
                        setError('Failed to add tag.');
                    }
                }
            }
            
            // Clear success message after 3 seconds
            setTimeout(() => setSuccessMessage(null), 3000);
        };
        
        // Check if plugin is configured
        if (!gaksData.isConfigured) {
            return wp.element.createElement(PluginSidebar, {
                name: 'gaks-keyword-suggestions',
                title: 'Keyword Suggestions',
                icon: KeywordIcon
            },
                wp.element.createElement('div', { className: 'gaks-sidebar-content' },
                    wp.element.createElement(Notice, {
                        status: 'warning',
                        isDismissible: false
                    }, 
                        wp.element.createElement('p', null, 'Please configure your Google Ads API credentials in '),
                        wp.element.createElement('a', { 
                            href: '/wp-admin/options-general.php?page=gaks-settings',
                            target: '_blank'
                        }, 'Settings → Keyword Suggestions')
                    )
                )
            );
        }
        
        return wp.element.createElement(
            wp.element.Fragment,
            null,
            wp.element.createElement(PluginSidebarMoreMenuItem, {
                target: 'gaks-keyword-suggestions',
                icon: KeywordIcon
            }, 'Keyword Suggestions'),
            wp.element.createElement(PluginSidebar, {
                name: 'gaks-keyword-suggestions',
                title: 'Keyword Suggestions',
                icon: KeywordIcon
            },
                wp.element.createElement('div', { className: 'gaks-sidebar-content' },
                    // Input section
                    wp.element.createElement(PanelBody, {
                        title: 'Seed Keywords',
                        initialOpen: true
                    },
                        wp.element.createElement(TextareaControl, {
                            label: 'Enter keywords (comma or newline separated)',
                            value: keywords,
                            onChange: setKeywords,
                            rows: 3,
                            placeholder: 'e.g., wordpress seo, blog optimization'
                        }),
                        wp.element.createElement(Button, {
                            isPrimary: true,
                            isBusy: loading,
                            disabled: loading,
                            onClick: fetchSuggestions,
                            className: 'gaks-fetch-button'
                        }, loading ? 'Fetching...' : 'Get Suggestions')
                    ),
                    
                    // Messages
                    error && wp.element.createElement(Notice, {
                        status: 'error',
                        isDismissible: true,
                        onRemove: () => setError(null),
                        className: 'gaks-notice'
                    }, error),
                    
                    successMessage && wp.element.createElement(Notice, {
                        status: 'success',
                        isDismissible: true,
                        onRemove: () => setSuccessMessage(null),
                        className: 'gaks-notice'
                    }, successMessage),
                    
                    // Loading state
                    loading && wp.element.createElement('div', { className: 'gaks-loading' },
                        wp.element.createElement(Spinner, null),
                        wp.element.createElement('p', null, 'Fetching keyword ideas...')
                    ),
                    
                    // Results
                    suggestions.length > 0 && wp.element.createElement(PanelBody, {
                        title: `Suggestions (${suggestions.length})`,
                        initialOpen: true
                    },
                        wp.element.createElement('div', { className: 'gaks-suggestions-list' },
                            suggestions.map((keyword, index) =>
                                wp.element.createElement(KeywordCard, {
                                    key: index,
                                    keyword: keyword,
                                    onInsert: insertKeyword
                                })
                            )
                        )
                    )
                )
            )
        );
    };
    
    // Register the plugin
    registerPlugin('gaks-keyword-suggestions', {
        render: KeywordSuggestionsSidebar,
        icon: KeywordIcon
    });
    
})(window.wp);

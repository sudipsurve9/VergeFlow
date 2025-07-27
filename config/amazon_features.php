<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Amazon-Style Features Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for all Amazon-style e-commerce features
    | implemented in VergeFlow platform.
    |
    */

    'reviews' => [
        'max_images_per_review' => env('MAX_REVIEW_IMAGES', 5),
        'max_image_size' => env('MAX_IMAGE_SIZE', 2048), // KB
        'allowed_image_types' => ['jpeg', 'jpg', 'png', 'webp'],
        'auto_approve' => env('AUTO_APPROVE_REVIEWS', false),
        'min_review_length' => 10,
        'max_review_length' => 5000,
        'helpful_vote_limit_per_user' => 1, // Per review
        'review_rate_limit' => env('REVIEW_RATE_LIMIT', 5), // Per day per user
    ],

    'recommendations' => [
        'cache_ttl' => env('RECOMMENDATION_CACHE_TTL', 3600), // 1 hour
        'max_recommendations' => 12,
        'min_orders_for_also_bought' => 2,
        'personalized_weight' => [
            'purchase_history' => 0.4,
            'recently_viewed' => 0.3,
            'cart_items' => 0.2,
            'trending' => 0.1,
        ],
        'fallback_to_popular' => true,
    ],

    'recently_viewed' => [
        'max_items_logged_in' => 20,
        'max_items_guest' => 10,
        'cleanup_after_days' => 30,
        'track_admin_views' => false,
    ],

    'notifications' => [
        'review_reminder_delay_days' => 3,
        'weekly_digest_day' => 'sunday', // Day of week
        'batch_size' => 100, // For bulk email sending
        'retry_attempts' => 3,
        'queue_connection' => env('QUEUE_CONNECTION', 'sync'),
    ],

    'search' => [
        'results_per_page' => 24,
        'max_search_suggestions' => 10,
        'cache_search_results' => true,
        'search_cache_ttl' => 1800, // 30 minutes
        'enable_fuzzy_search' => true,
        'min_search_length' => 2,
    ],

    'social_features' => [
        'enable_review_sharing' => true,
        'enable_reviewer_following' => true,
        'enable_badges' => true,
        'enable_contests' => true,
        'leaderboard_cache_ttl' => 3600,
        'badge_cache_ttl' => 7200, // 2 hours
    ],

    'performance' => [
        'enable_query_caching' => true,
        'cache_driver' => env('CACHE_DRIVER', 'file'),
        'enable_image_optimization' => true,
        'image_quality' => env('IMAGE_QUALITY', 85),
        'thumbnail_sizes' => [
            'small' => 150,
            'medium' => 300,
            'large' => 600,
        ],
        'lazy_load_images' => true,
    ],

    'security' => [
        'rate_limiting' => [
            'reviews' => '5:1440', // 5 per day
            'helpful_votes' => '50:60', // 50 per hour
            'search' => '100:60', // 100 per hour
            'api' => '60:60', // 60 per hour
        ],
        'content_filtering' => [
            'enable_profanity_filter' => true,
            'enable_spam_detection' => true,
            'min_review_quality_score' => 0.3,
        ],
        'image_validation' => [
            'scan_for_inappropriate_content' => false,
            'max_file_size' => 10240, // KB
            'require_image_verification' => true,
        ],
    ],

    'analytics' => [
        'track_product_views' => true,
        'track_search_queries' => true,
        'track_recommendation_clicks' => true,
        'track_review_helpfulness' => true,
        'retention_days' => 365,
    ],

    'ui' => [
        'theme' => [
            'primary_color' => '#007bff',
            'secondary_color' => '#6c757d',
            'success_color' => '#28a745',
            'warning_color' => '#ffc107',
            'danger_color' => '#dc3545',
        ],
        'star_rating' => [
            'style' => 'filled', // filled, outlined, half
            'color_filled' => '#ffc107',
            'color_empty' => '#e9ecef',
            'size' => '1.2rem',
        ],
        'pagination' => [
            'items_per_page' => 20,
            'show_page_numbers' => true,
            'show_first_last' => true,
        ],
    ],

    'email_templates' => [
        'review_reminder' => [
            'subject' => 'How was your recent purchase? Share your experience!',
            'from_name' => env('MAIL_FROM_NAME', 'VergeFlow'),
            'reply_to' => env('SUPPORT_EMAIL', 'support@vergeflow.com'),
        ],
        'helpful_vote' => [
            'subject' => 'Someone found your review helpful!',
            'from_name' => env('MAIL_FROM_NAME', 'VergeFlow'),
        ],
        'weekly_digest' => [
            'subject' => 'Your Weekly Review Activity & Recommendations',
            'from_name' => env('MAIL_FROM_NAME', 'VergeFlow'),
        ],
        'order_status' => [
            'subject_prefix' => 'Order #:order_id Status Update',
            'from_name' => env('MAIL_FROM_NAME', 'VergeFlow'),
        ],
    ],

    'admin' => [
        'reviews_per_page' => 20,
        'enable_bulk_operations' => true,
        'enable_csv_export' => true,
        'enable_analytics_dashboard' => true,
        'auto_refresh_dashboard' => false,
        'dashboard_refresh_interval' => 300, // seconds
    ],

    'api' => [
        'version' => 'v1',
        'rate_limit' => env('API_RATE_LIMIT', 60),
        'enable_cors' => true,
        'allowed_origins' => ['*'],
        'enable_api_documentation' => true,
    ],

    'integrations' => [
        'google_analytics' => [
            'tracking_id' => env('GOOGLE_ANALYTICS_ID'),
            'track_ecommerce' => true,
            'track_reviews' => true,
        ],
        'social_media' => [
            'facebook_app_id' => env('FACEBOOK_APP_ID'),
            'twitter_handle' => env('TWITTER_HANDLE', '@vergeflow'),
            'enable_social_login' => false,
        ],
        'payment' => [
            'stripe' => [
                'webhook_tolerance' => 300,
                'enable_payment_intents' => true,
            ],
        ],
    ],

    'maintenance' => [
        'cleanup_schedules' => [
            'old_recently_viewed' => 'daily',
            'expired_sessions' => 'hourly',
            'old_notifications' => 'weekly',
            'analytics_data' => 'monthly',
        ],
        'backup_schedules' => [
            'database' => 'daily',
            'files' => 'weekly',
            'logs' => 'monthly',
        ],
    ],
];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Redirect Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk sistem redirect otomatis ke admin area
    |
    */

    // Enable/disable admin redirect system
    'enabled' => true,

    // Admin base URL
    'admin_url' => '/admin',

    // Admin route name
    'admin_route' => 'admin.dashboard',

    // Paths yang dikecualikan dari redirect
    'excluded_paths' => [
        'login',
        'logout',
        'register',
        'password',
        'api',
        'broadcasting',
        'health',
        'admin',
        '_debugbar',
        'telescope',
        'horizon',
        'up', // Laravel health check
    ],

    // File extensions yang dikecualikan
    'excluded_extensions' => [
        'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico',
        'woff', 'woff2', 'ttf', 'eot', 'pdf', 'zip', 'json', 'xml',
        'txt', 'csv', 'xlsx', 'docx', 'mp4', 'mp3', 'wav'
    ],

    // User agents yang dikecualikan (bots, crawlers, etc)
    'excluded_user_agents' => [
        'bot',
        'crawler',
        'spider',
        'scraper',
        'curl',
        'wget',
        'postman'
    ],

    // Debug mode
    'debug' => false,

    // Notification settings
    'notifications' => [
        'enabled' => true,
        'duration' => 5000, // milliseconds
        'position' => 'top-right'
    ],

    // Tab system integration
    'tab_system' => [
        'preserve_tabs' => true,
        'allow_tab_refresh' => true,
        'prevent_browser_reload' => true
    ]
];
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security-related configuration options for the
    | application including rate limiting, file upload restrictions, and
    | other security measures.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for different types of requests
    |
    */
    'rate_limiting' => [
        'api' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'login' => [
            'max_attempts' => 5,
            'decay_minutes' => 15,
        ],
        'file_upload' => [
            'max_attempts' => 10,
            'decay_minutes' => 1,
        ],
        'sync' => [
            'max_attempts' => 3,
            'decay_minutes' => 5,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    |
    | Configure file upload restrictions and validation
    |
    */
    'file_upload' => [
        'max_size' => 10240, // 10MB in KB
        'allowed_mimes' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
        ],
        'allowed_extensions' => [
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'txt'
        ],
        'scan_for_malware' => false, // Set to true if you have malware scanning
        'quarantine_suspicious' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    |
    | Configure Content Security Policy headers
    |
    */
    'csp' => [
        'default_src' => ["'self'"],
        'script_src' => [
            "'self'",
            "'unsafe-inline'",
            "'unsafe-eval'",
            'https://www.googletagmanager.com',
            'https://www.google-analytics.com',
        ],
        'style_src' => [
            "'self'",
            "'unsafe-inline'",
            'https://fonts.googleapis.com',
        ],
        'font_src' => [
            "'self'",
            'https://fonts.gstatic.com',
        ],
        'img_src' => [
            "'self'",
            'data:',
            'https:',
            'http:',
        ],
        'connect_src' => [
            "'self'",
            'https://api.airtable.com',
        ],
        'frame_ancestors' => ["'none'"],
        'base_uri' => ["'self'"],
        'form_action' => ["'self'"],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Configure additional security headers
    |
    */
    'headers' => [
        'x_content_type_options' => 'nosniff',
        'x_frame_options' => 'DENY',
        'x_xss_protection' => '1; mode=block',
        'referrer_policy' => 'strict-origin-when-cross-origin',
        'permissions_policy' => 'geolocation=(), microphone=(), camera=()',
        'strict_transport_security' => [
            'enabled' => env('HTTPS_ENABLED', false),
            'max_age' => 31536000,
            'include_subdomains' => true,
            'preload' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Input Validation
    |--------------------------------------------------------------------------
    |
    | Configure input validation and sanitization
    |
    */
    'input_validation' => [
        'max_string_length' => 10000,
        'max_array_size' => 100,
        'sanitize_html' => true,
        'block_suspicious_patterns' => true,
        'suspicious_patterns' => [
            '/union\s+select/i',
            '/drop\s+table/i',
            '/delete\s+from/i',
            '/insert\s+into/i',
            '/update\s+set/i',
            '/or\s+1\s*=\s*1/i',
            '/and\s+1\s*=\s*1/i',
            '/<script/i',
            '/javascript:/i',
            '/vbscript:/i',
            '/onload=/i',
            '/onerror=/i',
            '/onclick=/i',
            '/onmouseover=/i',
            '/\.\.\//',
            '/\.\.\\\\/',
            '/%2e%2e%2f/',
            '/%2e%2e%5c/',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    |
    | Configure session security settings
    |
    */
    'session' => [
        'secure' => env('SESSION_SECURE', false),
        'http_only' => true,
        'same_site' => 'lax',
        'lifetime' => 120, // minutes
        'regenerate_on_login' => true,
        'regenerate_on_logout' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Security
    |--------------------------------------------------------------------------
    |
    | Configure password security requirements
    |
    */
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => true,
        'max_age_days' => 90,
        'history_count' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | API Security
    |--------------------------------------------------------------------------
    |
    | Configure API security settings
    |
    */
    'api' => [
        'require_https' => env('API_REQUIRE_HTTPS', false),
        'rate_limit_by_ip' => true,
        'rate_limit_by_user' => true,
        'log_all_requests' => true,
        'require_authentication' => true,
        'token_expiry_hours' => 24,
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring and Alerting
    |--------------------------------------------------------------------------
    |
    | Configure security monitoring and alerting
    |
    */
    'monitoring' => [
        'log_suspicious_activity' => true,
        'alert_on_multiple_failures' => true,
        'max_failed_attempts' => 5,
        'lockout_duration_minutes' => 15,
        'notify_admins_on_breach' => true,
        'admin_email' => env('ADMIN_EMAIL', 'admin@example.com'),
    ],
];





























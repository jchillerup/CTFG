<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Environment Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains environment security-related configuration options
    | including environment variable protection, secret management, and
    | configuration validation.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Environment Variable Protection
    |--------------------------------------------------------------------------
    |
    | Configure environment variable security settings
    |
    */
    'env_protection' => [
        'hide_sensitive_vars' => true,
        'sensitive_variables' => [
            'APP_KEY',
            'DB_PASSWORD',
            'MAIL_PASSWORD',
            'REDIS_PASSWORD',
            'CACHE_PASSWORD',
            'SESSION_DRIVER',
            'QUEUE_CONNECTION',
            'AIRTABLE_API_KEY',
            'BACKUP_ENCRYPTION_KEY',
            'DB_ENCRYPTION_KEY',
        ],
        'validate_required_vars' => true,
        'required_variables' => [
            'APP_KEY',
            'APP_ENV',
            'APP_DEBUG',
            'DB_CONNECTION',
            'DB_HOST',
            'DB_PORT',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Secret Management
    |--------------------------------------------------------------------------
    |
    | Configure secret management settings
    |
    */
    'secret_management' => [
        'encrypt_secrets' => true,
        'secret_encryption_key' => env('SECRET_ENCRYPTION_KEY'),
        'rotate_secrets' => true,
        'secret_rotation_days' => 90,
        'backup_secrets' => true,
        'secure_secret_storage' => env('SECURE_SECRET_PATH'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuration Validation
    |--------------------------------------------------------------------------
    |
    | Configure configuration validation settings
    |
    */
    'config_validation' => [
        'validate_on_boot' => true,
        'strict_mode' => env('APP_ENV') === 'production',
        'validate_database_config' => true,
        'validate_cache_config' => true,
        'validate_session_config' => true,
        'validate_mail_config' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | File Permissions
    |--------------------------------------------------------------------------
    |
    | Configure file permission security settings
    |
    */
    'file_permissions' => [
        'storage_directory' => 0755,
        'cache_directory' => 0755,
        'log_directory' => 0755,
        'config_directory' => 0644,
        'env_file' => 0600,
        'key_file' => 0600,
    ],

    /*
    |--------------------------------------------------------------------------
    | Directory Security
    |--------------------------------------------------------------------------
    |
    | Configure directory security settings
    |
    */
    'directory_security' => [
        'protect_sensitive_directories' => true,
        'sensitive_directories' => [
            'storage/app/private',
            'storage/logs',
            'config',
            '.env',
        ],
        'deny_direct_access' => true,
        'create_htaccess_files' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Handling
    |--------------------------------------------------------------------------
    |
    | Configure error handling security settings
    |
    */
    'error_handling' => [
        'hide_sensitive_errors' => env('APP_ENV') === 'production',
        'log_all_errors' => true,
        'error_log_path' => storage_path('logs/security-errors.log'),
        'max_error_log_size' => 10485760, // 10MB
        'error_log_retention_days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Mode Security
    |--------------------------------------------------------------------------
    |
    | Configure debug mode security settings
    |
    */
    'debug_security' => [
        'disable_debug_in_production' => true,
        'restrict_debug_access' => true,
        'debug_allowed_ips' => [
            '127.0.0.1',
            '::1',
        ],
        'debug_allowed_users' => [],
        'log_debug_access' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment Isolation
    |--------------------------------------------------------------------------
    |
    | Configure environment isolation settings
    |
    */
    'environment_isolation' => [
        'isolate_environments' => true,
        'prevent_cross_env_access' => true,
        'validate_environment_consistency' => true,
        'environment_specific_configs' => [
            'local' => [
                'debug' => true,
                'cache' => false,
                'log_level' => 'debug',
            ],
            'staging' => [
                'debug' => false,
                'cache' => true,
                'log_level' => 'info',
            ],
            'production' => [
                'debug' => false,
                'cache' => true,
                'log_level' => 'error',
            ],
        ],
    ],
];


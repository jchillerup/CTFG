<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains database security-related configuration options
    | including connection security, query logging, and access controls.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Connection Security
    |--------------------------------------------------------------------------
    |
    | Configure database connection security settings
    |
    */
    'connection_security' => [
        'use_ssl' => env('DB_SSL', false),
        'verify_ssl' => env('DB_VERIFY_SSL', true),
        'ssl_ca' => env('DB_SSL_CA'),
        'ssl_cert' => env('DB_SSL_CERT'),
        'ssl_key' => env('DB_SSL_KEY'),
        'connection_timeout' => 30,
        'read_timeout' => 30,
        'write_timeout' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Query Logging
    |--------------------------------------------------------------------------
    |
    | Configure database query logging for security monitoring
    |
    */
    'query_logging' => [
        'enabled' => env('DB_QUERY_LOGGING', false),
        'log_slow_queries' => true,
        'slow_query_threshold' => 1000, // milliseconds
        'log_all_queries' => false,
        'log_sensitive_operations' => true,
        'sensitive_operations' => [
            'DELETE',
            'DROP',
            'ALTER',
            'TRUNCATE',
            'CREATE',
            'GRANT',
            'REVOKE',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Access Control
    |--------------------------------------------------------------------------
    |
    | Configure database access control settings
    |
    */
    'access_control' => [
        'max_connections' => env('DB_MAX_CONNECTIONS', 100),
        'connection_pool_size' => env('DB_POOL_SIZE', 10),
        'idle_timeout' => 300, // seconds
        'max_query_size' => 1048576, // 1MB
        'max_result_set_size' => 10485760, // 10MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Protection
    |--------------------------------------------------------------------------
    |
    | Configure data protection and encryption settings
    |
    */
    'data_protection' => [
        'encrypt_sensitive_fields' => true,
        'sensitive_fields' => [
            'password',
            'email',
            'phone',
            'ssn',
            'credit_card',
            'api_key',
            'token',
        ],
        'encryption_key' => env('DB_ENCRYPTION_KEY'),
        'hash_passwords' => true,
        'password_hash_algorithm' => 'bcrypt',
        'password_hash_rounds' => 12,
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup Security
    |--------------------------------------------------------------------------
    |
    | Configure database backup security settings
    |
    */
    'backup_security' => [
        'encrypt_backups' => true,
        'backup_encryption_key' => env('BACKUP_ENCRYPTION_KEY'),
        'secure_backup_location' => env('SECURE_BACKUP_PATH'),
        'backup_retention_days' => 30,
        'verify_backup_integrity' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    |
    | Configure database audit logging
    |
    */
    'audit_logging' => [
        'enabled' => env('DB_AUDIT_LOGGING', true),
        'log_creates' => true,
        'log_updates' => true,
        'log_deletes' => true,
        'log_reads' => false,
        'include_user_info' => true,
        'include_ip_address' => true,
        'include_timestamp' => true,
        'audit_table' => 'audit_logs',
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | Configure database performance monitoring
    |
    */
    'performance_monitoring' => [
        'enabled' => env('DB_PERFORMANCE_MONITORING', true),
        'monitor_connection_pool' => true,
        'monitor_query_performance' => true,
        'monitor_deadlocks' => true,
        'monitor_lock_waits' => true,
        'alert_on_slow_queries' => true,
        'slow_query_threshold' => 2000, // milliseconds
    ],
];






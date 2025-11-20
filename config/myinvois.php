<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MyInvois Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the environment for MyInvois API.
    | Supported: "sandbox", "production"
    |
    */

    'environment' => env('MYINVOIS_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | MyInvois API URLs
    |--------------------------------------------------------------------------
    |
    | The base URLs for MyInvois API endpoints.
    | Sandbox and production environments have different URLs.
    |
    */

    'api_base_url' => env('MYINVOIS_API_BASE_URL', 'https://preprod-api.myinvois.hasil.gov.my'),

    'portal_url' => env('MYINVOIS_PORTAL_URL', 'https://preprod.myinvois.hasil.gov.my'),

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    |
    | MyInvois API endpoint paths
    |
    */

    'endpoints' => [
        'auth' => '/connect/token',
        'submit_documents' => '/api/v1.0/documentsubmissions',
        'get_submission' => '/api/v1.0/documentsubmissions/{submissionUid}',
        'get_document' => '/api/v1.0/documents/{uuid}/raw',
        'get_document_details' => '/api/v1.0/documents/{uuid}/details',
        'cancel_document' => '/api/v1.0/documents/state/{uuid}/state',
        'reject_document' => '/api/v1.0/documents/state/{uuid}/state',
        'validate_tin' => '/api/v1.0/taxpayer/validate/{tin}',
        'search_tin' => '/api/v1.0/taxpayer/{idType}/{idValue}/search',
        'get_recent_documents' => '/api/v1.0/documents/recent',
        'search_documents' => '/api/v1.0/documents/search',
        'get_notifications' => '/api/v1.0/notifications/taxpayer',
        'get_document_types' => '/api/v1.0/documenttypes',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Credentials
    |--------------------------------------------------------------------------
    |
    | OAuth 2.0 client credentials for MyInvois API authentication
    |
    */

    'credentials' => [
        'client_id' => env('MYINVOIS_CLIENT_ID'),
        'client_secret' => env('MYINVOIS_CLIENT_SECRET'),
        'grant_type' => 'client_credentials',
        'scope' => 'InvoicingAPI',
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Cache Settings
    |--------------------------------------------------------------------------
    |
    | MyInvois access tokens are valid for 60 minutes.
    | We cache them for 59 minutes to avoid expiry issues.
    |
    */

    'token_cache' => [
        'enabled' => true,
        'key' => 'myinvois_access_token',
        'ttl' => 59 * 60, // 59 minutes in seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | MyInvois API rate limits (requests per minute)
    |
    */

    'rate_limits' => [
        'auth' => 12,           // Authentication: 12 requests/minute
        'submission' => 100,    // Document submission: 100 requests/minute
        'status' => 300,        // Status checks: 300 requests/minute
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Timeouts
    |--------------------------------------------------------------------------
    |
    | HTTP request timeout settings (in seconds)
    |
    */

    'timeout' => [
        'connect' => 10,        // Connection timeout
        'request' => 120,       // Request timeout (2 minutes)
    ],

    /*
    |--------------------------------------------------------------------------
    | Document Settings
    |--------------------------------------------------------------------------
    |
    | Settings for e-invoice document generation and submission
    |
    */

    'document' => [
        'version' => '1.0',     // Document version (1.0 = no signature, 1.1 = with signature)
        'format' => 'JSON',     // JSON or XML
        'max_size_kb' => 300,   // Maximum document size in KB
        'max_batch_size' => 100, // Maximum documents per submission
        'max_submission_mb' => 5, // Maximum submission size in MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Polling Settings
    |--------------------------------------------------------------------------
    |
    | Settings for polling document status from MyInvois
    |
    */

    'polling' => [
        'enabled' => true,
        'interval_seconds' => 300, // 5 minutes (as per scheduler)
        'min_interval_seconds' => 3, // Minimum 3 seconds between status checks (API recommendation)
        'max_retries' => 20,     // Stop polling after 20 attempts (~1.5 hours)
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Settings
    |--------------------------------------------------------------------------
    |
    | Job retry configuration for failed submissions
    |
    */

    'retry' => [
        'max_attempts' => 5,
        'backoff_seconds' => [10, 30, 60, 120, 300], // Exponential backoff
    ],

    /*
    |--------------------------------------------------------------------------
    | Company Profile Source
    |--------------------------------------------------------------------------
    |
    | Company information is fetched from the company_profiles database table.
    | Specify the default company profile ID or use the first active profile.
    |
    | NOTE: Company data (TIN, MSIC, name, address, etc.) will be loaded
    | dynamically from App\Models\CompanyProfile instead of .env
    |
    */

    'company' => [
        'source' => 'database',  // Always fetch from database
        'model' => \App\Models\CompanyProfile::class,
        'default_id' => env('MYINVOIS_DEFAULT_COMPANY_ID', 1), // Default company profile ID
        'fallback_to_first' => true, // Use first company if default not found
    ],

    /*
    |--------------------------------------------------------------------------
    | Document Type Codes
    |--------------------------------------------------------------------------
    |
    | MyInvois document type codes
    |
    */

    'document_types' => [
        'invoice' => '01',
        'credit_note' => '02',
        'debit_note' => '03',
        'refund_note' => '04',
        'self_billed_invoice' => '11',
        'self_billed_credit_note' => '12',
        'self_billed_debit_note' => '13',
        'self_billed_refund_note' => '14',
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable detailed logging for debugging
    |
    */

    'logging' => [
        'enabled' => env('MYINVOIS_LOGGING_ENABLED', true),
        'channel' => env('MYINVOIS_LOG_CHANNEL', 'myinvois'),
        'log_requests' => true,
        'log_responses' => true,
        'log_payloads' => env('APP_DEBUG', false), // Only log full payloads in debug mode
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable/disable specific features
    |
    */

    'features' => [
        'tin_validation' => true,           // Validate TIN before submission
        'auto_retry' => true,                // Auto-retry failed submissions
        'status_polling' => true,            // Auto-poll for status updates
        'notifications' => false,            // Fetch MyInvois notifications (optional)
        'document_search' => false,          // Document search feature (optional)
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Validation settings for e-invoice data
    |
    */

    'validation' => [
        'tin_format' => '/^C[0-9]{10}$/', // Company TIN format: C followed by 10 digits
        'required_customer_fields' => ['tin', 'company_name', 'address_line_1', 'city', 'state', 'postcode'],
        'required_company_fields' => ['tin', 'company_name', 'msic_code', 'business_registration_no', 'address_line_1', 'city', 'state', 'postcode'],
    ],

];

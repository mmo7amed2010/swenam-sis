<?php

/**
 * DataTable Configuration
 *
 * Configures the HandlesDataTableRequests trait behavior for server-side
 * DataTables processing across the application.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed Page Lengths
    |--------------------------------------------------------------------------
    |
    | These are the valid page size options that users can select.
    | Any request with a length not in this list will default to default_length.
    |
    */
    'allowed_lengths' => [10, 15, 25, 50, 100],

    /*
    |--------------------------------------------------------------------------
    | Default Page Length
    |--------------------------------------------------------------------------
    |
    | The default number of records to display per page when no length
    | is specified or an invalid length is provided.
    |
    */
    'default_length' => 15,

    /*
    |--------------------------------------------------------------------------
    | Cache TTL (Time To Live)
    |--------------------------------------------------------------------------
    |
    | Duration in seconds to cache DataTable results when caching is enabled.
    | Default: 300 seconds (5 minutes)
    |
    */
    'cache_ttl' => 300,

    /*
    |--------------------------------------------------------------------------
    | Enable Caching
    |--------------------------------------------------------------------------
    |
    | When enabled, DataTable responses can be cached per-user for improved
    | performance. Controllers must explicitly opt-in by providing a cache key.
    | Cache keys automatically include user_id and user_type for security.
    |
    */
    'enable_caching' => false,

];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Anthropic API Key
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Anthropic API Key. This will be used to
    | authenticate with the Anthropic API - you can find your API key
    | on your Anthropic dashboard, at https://console.anthropic.com
    */

    'api_key' => env('ANTHROPIC_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Anthropic Base URL
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Anthropic API base URL used to make requests.
    | This is needed if using a custom API endpoint. Defaults to: api.anthropic.com
    */

    'base_url' => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com'),

    /*
    |--------------------------------------------------------------------------
    | Anthropic API Version
    |--------------------------------------------------------------------------
    |
    | The API version to use for requests. Anthropic uses date-based versioning.
    | See: https://docs.anthropic.com/claude/reference/versions
    */

    'version' => env('ANTHROPIC_API_VERSION', '2023-06-01'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 180 seconds.
    */

    'request_timeout' => env('ANTHROPIC_REQUEST_TIMEOUT', 240),
];



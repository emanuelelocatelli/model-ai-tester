<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Gemini API Key
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Google Gemini API Key. This will be used to
    | authenticate with the Gemini API - you can find your API key
    | on your Google AI Studio dashboard, at https://aistudio.google.com
    */

    'api_key' => env('GEMINI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Gemini Base URL
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Gemini API base URL used to make requests.
    | This is needed if using a custom API endpoint.
    */

    'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com'),

    /*
    |--------------------------------------------------------------------------
    | Gemini API Version
    |--------------------------------------------------------------------------
    |
    | The API version to use for requests.
    */

    'version' => env('GEMINI_API_VERSION', 'v1beta'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 240 seconds.
    */

    'request_timeout' => env('GEMINI_REQUEST_TIMEOUT', 240),
];


<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DeepL API Key
    |--------------------------------------------------------------------------
    |
    | Your DeepL API authentication key. You can find this in your DeepL
    | account settings. This key is required for all API requests.
    |
    */

    'api_key' => env('DEEPL_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | DeepL API URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the DeepL API. Use the free API endpoint if you have
    | a free plan, or the pro endpoint if you have a paid subscription.
    | Free: https://api-free.deepl.com/v2
    | Pro: https://api.deepl.com/v2
    |
    */

    'api_url' => env('DEEPL_API_URL', 'https://api-free.deepl.com/v2'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum time in seconds to wait for API requests to complete.
    | Document translation may take longer, so we set a generous timeout.
    |
    */

    'timeout' => env('DEEPL_TIMEOUT', 300),

    /*
    |--------------------------------------------------------------------------
    | Supported Target Languages
    |--------------------------------------------------------------------------
    |
    | List of target languages supported by DeepL for translation.
    | Format: 'code' => 'Display Name'
    |
    */

    'target_languages' => [
        'BG' => 'Bulgarian',
        'CS' => 'Czech',
        'DA' => 'Danish',
        'DE' => 'German',
        'EL' => 'Greek',
        'EN-GB' => 'English (British)',
        'EN-US' => 'English (American)',
        'ES' => 'Spanish',
        'ET' => 'Estonian',
        'FI' => 'Finnish',
        'FR' => 'French',
        'HU' => 'Hungarian',
        'ID' => 'Indonesian',
        'IT' => 'Italian',
        'JA' => 'Japanese',
        'KO' => 'Korean',
        'LT' => 'Lithuanian',
        'LV' => 'Latvian',
        'NB' => 'Norwegian',
        'NL' => 'Dutch',
        'PL' => 'Polish',
        'PT-BR' => 'Portuguese (Brazilian)',
        'PT-PT' => 'Portuguese (European)',
        'RO' => 'Romanian',
        'RU' => 'Russian',
        'SK' => 'Slovak',
        'SL' => 'Slovenian',
        'SV' => 'Swedish',
        'TR' => 'Turkish',
        'UK' => 'Ukrainian',
        'ZH' => 'Chinese (simplified)',
    ],

];


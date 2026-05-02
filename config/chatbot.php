<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Chatbot API Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration is used for the chatbot integration in the admin
    | chat system. You can configure the API endpoint and authentication
    | credentials here.
    |
    */

    'api_endpoint' => env('CHATBOT_API_ENDPOINT', ''),

    'api_key' => env('CHATBOT_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Chatbot Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum time in seconds to wait for a response from the chatbot API.
    | Default is 5 seconds as per requirements.
    |
    */

    'timeout' => env('CHATBOT_TIMEOUT', 5),

    /*
    |--------------------------------------------------------------------------
    | Enable Rule-Based Fallback
    |--------------------------------------------------------------------------
    |
    | When enabled, the chatbot will use rule-based responses if the API
    | endpoint is not configured or fails.
    |
    */

    'enable_rule_based' => env('CHATBOT_ENABLE_RULE_BASED', true),

];

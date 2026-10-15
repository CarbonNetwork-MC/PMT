<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Placeholder
    |--------------------------------------------------------------------------
    |
    | The default placeholder text shown in the select component when no
    | option is selected.
    |
    */
    'placeholder' => env('ASYNC_SELECT_PLACEHOLDER', 'Select an option'),

    /*
    |--------------------------------------------------------------------------
    | Minimum Search Length
    |--------------------------------------------------------------------------
    |
    | The minimum number of characters required before a search is triggered.
    | This helps reduce unnecessary API calls.
    |
    */
    'min_search_length' => env('ASYNC_SELECT_MIN_SEARCH_LENGTH', 2),

    /*
    |--------------------------------------------------------------------------
    | Search Delay (milliseconds)
    |--------------------------------------------------------------------------
    |
    | The delay in milliseconds before triggering a search after the user
    | stops typing. This helps reduce the number of API calls.
    |
    */
    'search_delay' => env('ASYNC_SELECT_SEARCH_DELAY', 300),

    /*
    |--------------------------------------------------------------------------
    | Default Search Parameter Name
    |--------------------------------------------------------------------------
    |
    | The query parameter name used when sending search queries to the endpoint.
    |
    */
    'search_param' => env('ASYNC_SELECT_SEARCH_PARAM', 'search'),

    /*
    |--------------------------------------------------------------------------
    | Default Selected Parameter Name
    |--------------------------------------------------------------------------
    |
    | The query parameter name used when fetching selected items from the
    | selectedEndpoint.
    |
    */
    'selected_param' => env('ASYNC_SELECT_SELECTED_PARAM', 'selected'),

    /*
    |--------------------------------------------------------------------------
    | Autoload
    |--------------------------------------------------------------------------
    |
    | When enabled, the component will automatically load options when
    | mounted, even without a search query.
    |
    */
    'autoload' => env('ASYNC_SELECT_AUTOLOAD', false),

    /*
    |--------------------------------------------------------------------------
    | Multiple Selection
    |--------------------------------------------------------------------------
    |
    | Enable multiple selection mode by default. You can override this on a
    | per-component basis by passing :multiple="true|false".
    |
    */
    'multiple' => env('ASYNC_SELECT_MULTIPLE', false),

    /*
    |--------------------------------------------------------------------------
    | UI Theme
    |--------------------------------------------------------------------------
    |
    | Default UI theme for the component. Options: 'tailwind' or 'bootstrap'.
    | You can override this on a per-component basis by passing :ui="tailwind"
    | or :ui="bootstrap".
    |
    */
    'ui' => env('ASYNC_SELECT_UI', 'tailwind'),

    /*
    |--------------------------------------------------------------------------
    | CSS Class Prefix
    |--------------------------------------------------------------------------
    |
    | All CSS classes are prefixed with 'las-' (Livewire Async Select)
    | to avoid conflicts with your application's styles. This package uses
    | Tailwind CSS utility classes with this prefix.
    |
    */
    'class_prefix' => 'las-',

    /*
    |--------------------------------------------------------------------------
    | Internal Authentication
    |--------------------------------------------------------------------------
    |
    | Enable internal authentication globally for all AsyncSelect components.
    | When enabled, all components will automatically use internal auth for
    | internal endpoints. You can override this per-component if needed.
    |
    */
    'use_internal_auth' => env('ASYNC_SELECT_USE_INTERNAL_AUTH', true),

    /*
    |--------------------------------------------------------------------------
    | Internal Authentication Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for internal authentication when making requests to
    | endpoints on the same domain. This allows the AsyncSelect component
    | to automatically authenticate requests using signed tokens.
    |
    */
    'internal' => [
        /*
         * Secret key for signing internal auth tokens.
         * Generate a random key: php -r "echo base64_encode(random_bytes(32));"
         * Set this in your .env file as ASYNC_SELECT_INTERNAL_SECRET
         */
        'secret' => env('ASYNC_SELECT_INTERNAL_SECRET', ''),

        /*
         * Previous secret key for key rotation support.
         * When rotating keys, set the new key as ASYNC_SELECT_INTERNAL_SECRET
         * and keep the old key here temporarily. Tokens signed with either key
         * will be accepted during the rotation period.
         * Set this in your .env file as ASYNC_SELECT_INTERNAL_PREVIOUS_SECRET
         */
        'previous_secret' => env('ASYNC_SELECT_INTERNAL_PREVIOUS_SECRET', ''),

        /*
         * Time-to-live for nonce cache entries (in seconds).
         * Prevents replay attacks by caching used nonces.
         */
        'nonce_ttl' => env('ASYNC_SELECT_INTERNAL_NONCE_TTL', 120),

        /*
         * Time skew tolerance in seconds for token expiry validation.
         */
        'skew' => env('ASYNC_SELECT_INTERNAL_SKEW', 60),
    ],
];

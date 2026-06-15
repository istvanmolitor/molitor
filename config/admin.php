<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Panel Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the admin panel settings for your application.
    |
    */

    'name' => env('ADMIN_NAME', 'Admin Panel'),

    'prefix' => env('ADMIN_PREFIX', 'admin'),

    'route_namespace' => 'Molitor\\Admin\\Http\\Controllers',

    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard
    |--------------------------------------------------------------------------
    */

    'dashboard' => [
        'enabled' => true,
        'route' => 'dashboard',
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Menu
    |--------------------------------------------------------------------------
    */

    'menu' => [
        'items' => [
            // Add your menu items here
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Search
    |--------------------------------------------------------------------------
    |
    | Register AdminSearch subclasses to extend what the admin search covers.
    |
    */

    'search' => [
        'searchers' => [
            \Molitor\User\Search\UserSearch::class,
            \Molitor\Cms\Search\PostSearch::class,
            \Molitor\Order\Search\OrderSearch::class,
            \Molitor\Customer\Search\CustomerSearch::class,
            \Molitor\Product\Search\ProductSearch::class,
        ],
    ],
];

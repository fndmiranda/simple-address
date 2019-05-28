<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Adapters
    |--------------------------------------------------------------------------
    |
    | Here you can specify api adapters for address search by postcode.
    |
    */

    'apis' => [
        Fndmiranda\SimpleAddress\Adapters\ViaCepAdapter::class,
        Fndmiranda\SimpleAddress\Adapters\PostmonAdapter::class,
        Fndmiranda\SimpleAddress\Adapters\WidenetAdapter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Priority
    |--------------------------------------------------------------------------
    |
    | Forces the priority of the Api`s list or randomizes the list as load balancing.
    |
    */

    'force_priority' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the application to access the Google Maps API, check the
    | request limits of the contracted key for use, if limits are reached,
    | it may compromise the proper functioning of the application.
    |
    */

    'google_maps_key' => env('ADDRESS_GOOGLE_MAPS_KEY', null),

    'google_url_api_geocode' => 'https://maps.googleapis.com/maps/api/geocode/json',

    /*
    |--------------------------------------------------------------------------
    | Database
    |--------------------------------------------------------------------------
    |
    | Here you can specify whether to manage and store the address data
    | and the column type of your primary key that will make the polymorphic relationship.
    | The types are uuid, bigInteger and integer.
    |
    */

    'manager_address' => true,

    'column_type' => 'integer',
];

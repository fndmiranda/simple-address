<?php

return [

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
    | Polymorphic Relationship
    |--------------------------------------------------------------------------
    |
    | Here you can specify the column type of your primary key that will
    | make the polymorphic relationship.
    | The types are uuid, bigInteger and integer
    |
    */

    'column_type' => env('ADDRESS_COLUMN_TYPE', 'uuid'),

    'force_priority' => false,

    'api' => [
        Fndmiranda\Address\Adapters\ViaCepAdapter::class,
    ],

];

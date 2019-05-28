<?php

namespace Fndmiranda\SimpleAddress\Pivot;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class AddressPivot extends MorphPivot
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'addressable_type', 'addressable_id', 'address_id',
    ];
}
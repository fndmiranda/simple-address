<?php

namespace Fndmiranda\Address\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array get(string $postcode, bool $geolocation = false)
 *
 * @see \Fndmiranda\Address\Address
 */
class Address extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'address';
    }
}

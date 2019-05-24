<?php

namespace Fndmiranda\Address\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array search(string|int $postcode, bool $geolocation = false)
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

<?php

namespace Fndmiranda\SimpleAddress\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array search(string|int $postcode, bool $geolocation = false)
 * @method static array|null geocoding(array|\Fndmiranda\SimpleAddress\Entities\Address $address, array $complements = [])
 *
 * @see \Fndmiranda\SimpleAddress\Address
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

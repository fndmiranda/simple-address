<?php

namespace Fndmiranda\SimpleAddress\Repositories;

use Fndmiranda\SimpleAddress\Entities\Address;

class AddressRepository
{
    /**
     * Get a address by the given postcode.
     *
     * @param string $postcode
     * @return \Fndmiranda\SimpleAddress\Entities\Address
     */
    public function findByPostcode($postcode)
    {
        return Address::where('postcode', $postcode)->with(['neighborhood.city.state'])->first();
    }
}

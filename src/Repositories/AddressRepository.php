<?php

namespace Fndmiranda\Repositories;

use Fndmiranda\Address\Entities\Address;

class AddressRepository
{
    /**
     * Get a address by the given postcode.
     *
     * @param string $postcode
     * @return \Fndmiranda\Address\Entities\Address
     */
    public function findByPostcode($postcode)
    {
        return Address::where('postcode', $postcode)->with(['neighborhood.city.state'])->first();
    }
}

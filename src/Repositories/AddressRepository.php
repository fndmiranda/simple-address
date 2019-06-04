<?php

namespace Fndmiranda\SimpleAddress\Repositories;

use Fndmiranda\SimpleAddress\Entities\Address;

class AddressRepository
{
    /**
     * Get a address by the given ID.
     *
     * @param string $id
     * @return \Fndmiranda\SimpleAddress\Entities\Address
     */
    public function find($id)
    {
        return Address::where('id', $id)->with(['neighborhood.city.state'])->first();
    }

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

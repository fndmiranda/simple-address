<?php

namespace Fndmiranda\Address;

use Illuminate\Support\Arr;

class Address
{
    /**
     * Search address by postcode in Api`s.
     *
     * @param string|int $postcode
     * @param bool $geolocation
     * @return array
     */
    public function search($postcode, $geolocation = false)
    {
        $postcode = $this->postcode($postcode);

        $selected = Arr::random(config('address.api'));

        $adapter = app($selected);

        $data = $adapter->search($postcode);

        dump($data);

        return $data;
    }

    /**
     * Format and clear postcode.
     *
     * @param $value
     * @return string
     */
    private function postcode($value)
    {
        $postcode = str_replace('-', '', $value);
        $postcode = str_pad($postcode, 8, '0', STR_PAD_LEFT);

        return $postcode;
    }
}

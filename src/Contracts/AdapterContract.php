<?php

namespace Fndmiranda\SimpleAddress\Contracts;

interface AdapterContract
{
    /**
     * Search external address by postcode.
     *
     * @param $postcode
     * @return array|bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search($postcode);

    /**
     * Prepare address data.
     *
     * @param $data
     * @return array
     */
    public function prepare($data);
}

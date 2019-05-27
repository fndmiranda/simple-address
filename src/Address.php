<?php

namespace Fndmiranda\Address;

use Fndmiranda\Repositories\AddressRepository;
use Fndmiranda\Repositories\StateRepository;

class Address
{
    use Geocoding;

    /**
     * The address repository instance.
     *
     * @var AddressRepository
     */
    protected $addressRepository;

    /**
     * The state repository instance.
     *
     * @var StateRepository
     */
    protected $stateRepository;

    /**
     * Create a new address instance.
     *
     * @param \Fndmiranda\Repositories\AddressRepository $addressRepository
     * @param \Fndmiranda\Repositories\StateRepository $stateRepository
     * @return void
     */
    public function __construct(
        AddressRepository $addressRepository,
        StateRepository $stateRepository
    )
    {
        $this->addressRepository = $addressRepository;
        $this->stateRepository = $stateRepository;
    }

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

        if (config('address.manager_address')) {
            $entity = $this->addressRepository->findByPostcode($postcode);

            if ($entity) {
                return $entity;
            }
        }

        return $this->external($postcode);
    }

    /**
     * Search in Api`s external by postcode.
     *
     * @param $postcode
     * @return Entities\Address
     */
    private function external($postcode)
    {
        $apis = config('address.apis');

        if (! config('address.force_priority')) {
            shuffle($apis);
        }

        foreach ($apis as $api) {
            $adapter = app($api);

            $data = $adapter->search($postcode);

            if ($data) {
                return $this->prepare($data);
            }
        }
    }

    /**
     * Prepare data returned by a adapter.
     *
     * @param $data
     * @return Entities\Address
     */
    private function prepare($data)
    {
        $data['postcode'] = $this->postcode($data['postcode']);

        if (config('address.manager_address')) {
            $state = $this->stateRepository->firstOrCreate(strtoupper($data['state']));
            $city = $state->cities()->firstOrCreate(['name' => $data['city']]);
            $neighborhood = $city->neighborhoods()->firstOrCreate(['name' => $data['neighborhood']]);
            $address = $neighborhood->addresses()->firstOrCreate(['name' => $data['address'], 'postcode' => $data['postcode']]);

            return $this->addressRepository->findByPostcode($address['postcode']);
        }

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

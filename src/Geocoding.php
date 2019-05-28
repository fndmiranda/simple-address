<?php

namespace Fndmiranda\SimpleAddress;

use Fndmiranda\SimpleAddress\Entities\Address;

trait Geocoding
{
    /**
     * Get the latitude and longitude coordinates from an address.
     *
     * @param array|Address $address
     * @param array $complements
     * @return null|array
     */
    public function geocoding($address, $complements = [])
    {
        $mapping = [
            'address',
            'number',
            'neighborhood',
            'city',
            'state',
            'postcode',
        ];

        $params = [];

        if ($address instanceof Address) {
            $params['address'] = $address->name;
            $params['postcode'] = $address->postcode;
            $params['neighborhood'] = $address->neighborhood->name;
            $params['city'] = $address->neighborhood->city->name;
            $params['state'] = $address->neighborhood->city->state->name;
            if (!empty($address->pivot->number)) {
                $params['number'] = $address->pivot->number;
            }
        } elseif (is_array($address)) {
            foreach ($address as $key => $value) {
                if (in_array($key, $mapping)) {
                    $params[$key] = $value;
                }
            }
        }

        $params = array_merge($params, $complements);

        uksort($params, function ($a, $b) use ($mapping) {
            $cmpa = array_search($a, $mapping);
            $cmpb = array_search($b, $mapping);
            return ($cmpa > $cmpb) ? 1 : -1;
        });

        $queryString = str_replace(' ', '+', implode(',', $params));

        $client = new \GuzzleHttp\Client();

        $response = $client->get(config('address.google_url_api_geocode'), [
            'query' => [
                'address' => $queryString,
                'key' => config('address.google_maps_key'),
            ]
        ]);

        $data = json_decode((string) $response->getBody(), true);

        if (!empty($data) && $data['status'] == 'OK' && !empty($data['results'])) {
            return [
                'lat' => $data['results'][0]['geometry']['location']['lat'],
                'lng' => $data['results'][0]['geometry']['location']['lng'],
            ];
        }
    }
}

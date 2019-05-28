<?php

namespace Fndmiranda\SimpleAddress\Adapters;

use Fndmiranda\SimpleAddress\Contracts\AdapterContract;
use GuzzleHttp\Psr7\Request;

class WidenetAdapter implements AdapterContract
{
    /**
     * Search external address by postcode.
     *
     * @param $postcode
     * @return array|bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search($postcode)
    {
        $client = new \GuzzleHttp\Client();
        $request = new Request('GET', 'http://apps.widenet.com.br/busca-cep/api/cep/'.$postcode.'.json');
        $response = $client->send($request);

        if ($response->getStatusCode() == 200) {
            $data = json_decode((string) $response->getBody(), true);

            if ((bool) $data['status']) {
                return $this->prepare($data);
            }
        }

        return false;
    }

    /**
     * Prepare address data.
     *
     * @param $data
     * @return array
     */
    public function prepare($data)
    {
        return [
            'postcode' => $data['code'],
            'address' => $data['address'],
            'neighborhood' => $data['district'],
            'city' => $data['city'],
            'state' => $data['state'],
        ];
    }
}

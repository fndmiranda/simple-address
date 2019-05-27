<?php

namespace Fndmiranda\Address\Adapters;

use Fndmiranda\Contracts\AdapterContract;
use GuzzleHttp\Psr7\Request;

class PostmonAdapter implements AdapterContract
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
        $request = new Request('GET', 'https://api.postmon.com.br/v1/cep/'.$postcode.'?format=json');
        $response = $client->send($request);

        if ($response->getStatusCode() != 200) {
            return false;
        }

        $data = json_decode((string) $response->getBody(), true);

        return $this->prepare($data);
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
            'postcode' => $data['cep'],
            'address' => $data['logradouro'],
            'neighborhood' => $data['bairro'],
            'city' => $data['cidade'],
            'state' => $data['estado'],
        ];
    }
}

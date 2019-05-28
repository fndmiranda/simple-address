<?php

namespace Fndmiranda\SimpleAddress\Adapters;

use Fndmiranda\SimpleAddress\Contracts\AdapterContract;
use GuzzleHttp\Psr7\Request;

class ViaCepAdapter implements AdapterContract
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
        $request = new Request('GET', 'https://viacep.com.br/ws/'.$postcode.'/json/');
        $response = $client->send($request);

        $data = json_decode((string) $response->getBody(), true);

        if (!empty($data['erro'])) {
            return false;
        }

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
            'city' => $data['localidade'],
            'state' => $data['uf'],
        ];
    }
}

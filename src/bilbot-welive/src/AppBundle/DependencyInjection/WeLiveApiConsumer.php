<?php

namespace AppBundle\DependencyInjection;

use GuzzleHttp\Client;

class WeLiveApiConsumer
{
    private $endpoint;

    public function __construct(
        $endpoint
    )
    {
        $this->endpoint = $endpoint;
    }

    private function buildAddress($dataset, $resource) {
        return $this->endpoint . $dataset . '/resource/' . $resource . '/query?origin=ANY';
    }

    public function query($dataset, $resource, $query)
    {
        $client = new Client(
            ['base_uri' =>
                $this->buildAddress(
                $dataset,
                $resource)
            ]
        );

        $res = $client->post(
            '',
            [
                'Content-Type' => 'Content-Type: text/plain',
                'Accept' => 'application/json',
                'body' => $query
            ]);

        return json_decode($res->getBody());
    }
}
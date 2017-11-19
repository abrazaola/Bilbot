<?php

namespace AppBundle\DependencyInjection;


use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;

class WatsonApiConsumer
{
    private $client;
    private $username;
    private $password;
    private $endpoint;

    public function __construct($username, $password, $endpoint)
    {
        $this->username = $username;
        $this->password = $password;
        $this->endpoint = $endpoint;

        $this->client = new Client(['base_uri' => $endpoint]);
    }

    public function understandme($text)
    {
        try {
            $res = $this->client->get(
                'analyze',
                [
                    'auth' => [
                        $this->username,
                        $this->password
                    ],
                    'query' => [
                        'version' => '2017-02-27',
                        'text' => $text,
                        'features' => 'sentiment,keywords,concepts,entities',
                        'keywords.sentiment' => 'true'
                    ]
                ])->getBody();
        } catch (Exception $e) {
            $res = json_encode(['error' => $e->getMessage()]);
        }

        return json_decode($res);
    }
}
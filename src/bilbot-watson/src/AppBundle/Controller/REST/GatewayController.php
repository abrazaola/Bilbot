<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GatewayController extends FOSRestController
{
    public function understandMeAction(Request $request) {
        $text = $request->query->get('text');

        if (null === $text) {
            return new JsonResponse(['error' => 'Empty text'], 300);
        }

        $client = new Client(['base_uri' => $this->get('watson_api_endpoint')]);
        $res = $client->get(
            'analyze',
            ['auth' => [$this->get('watson_api_username'), $this->get('watson_api_username')],
                'query' => [
                    'version' => '2017-02-27',
                    'text' => $text,
                    'features' => 'sentiment,keywords,concepts,entities',
                    'keywords.sentiment' => 'true'
                ]
            ]);

        return new JsonResponse($res->getBody(), 200);
    }
}
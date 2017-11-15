<?php

namespace AppBundle\Controller\REST;


use GuzzleHttp\Client;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GatewayController extends FOSRestController
{
    public function understandMeAction(Request $request) {
        $text = $request->query->get('text');

        if (null === $text) {
            return new JsonResponse(['error' => 'Empty text'], 300);
        }

        $client = new Client(['base_uri' => $this->container->getParameter('watson_api_endpoint')]);
        $res = $client->get(
            'analyze',
            [
                'auth' => [
                $this->container->getParameter('watson_api_username'),
                $this->container->getParameter('watson_api_password')
            ],
            'query' => [
                'version' => '2017-02-27',
                'text' => $text,
                'features' => 'sentiment,keywords,concepts,entities',
                'keywords.sentiment' => 'true'
            ]
            ]);

        $data = [
            'text'    => $res->getBody()->getContents(),
        ];

        return new JsonResponse($data, 200);
    }
}
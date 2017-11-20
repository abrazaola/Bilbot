<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GatewayController extends FOSRestController
{
    public function understandMeAction(Request $request) {
        $text = $request->get('text');

        if (null === $text) {
            return new JsonResponse(['error' => 'Empty text'], 300);
        }

        $res = $this->get('watson_api_consumer')->query($text);

        $data = [
            'analysis'    => $res,
        ];

        return new JsonResponse($data, 200);
    }

    public function agendaAnalysisAction(Request $request) {
        $text = $request->get('text');

        if (null === $text) {
            return new JsonResponse(['error' => 'Empty text'], 300);
        }

        $res = $this->get('watson_api_consumer')->query($text);

        $data = [
            'analysis'    => $res,
        ];

        return new JsonResponse($data, 200);
    }
}
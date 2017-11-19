<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class GatewayController extends FOSRestController
{
    public function bilbaoClubsAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query('guia-de-asociaciones-de-bilbao', '2fb58eaa-3815-4b5f-bce7-91b9ec827640');

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }
}
<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ClubsController extends FOSRestController
{
    const DATASET = 'guia-de-asociaciones-de-bilbao';

    const RESOURCE = '2fb58eaa-3815-4b5f-bce7-91b9ec827640';

    public function debugAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                'select * from results limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }
}
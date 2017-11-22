<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class NightlyBusRoutesController extends FOSRestController
{
    const DATASET = 'lineas-nocturnas-de-autobuses-bilbobus';

    const RESOURCE = '90d70b5f-197c-4291-ab88-4b16e8fe86f3';

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
<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class RestaurantsController extends FOSRestController
{
    const DATASET = 'restaurante-sidrerias-y-bodegas-de-euskadi1';

    const RESOURCE = 'cb835659-1d74-4131-82dd-35fa6d88a511';

    public function debugAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                'select * from rootTable limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }
}
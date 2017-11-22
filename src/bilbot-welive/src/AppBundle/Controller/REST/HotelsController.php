<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class HotelsController extends FOSRestController
{
    const DATASET = 'alojamientos-turisticos-de-euskadi';

    const RESOURCE = '78a18256-cfba-4f51-825f-7e2e0f48b822';

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
<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class BusesPerRouteController extends FOSRestController
{
    const DATASET = 'paradas-de-autobuses-bilbobus-por-recorrido-de-las-lineas';

    const RESOURCE = '420ced04-7552-4e27-85d8-8d06484c3e37';

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
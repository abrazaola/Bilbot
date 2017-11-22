<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class DailyBusRoutesController extends FOSRestController
{
    const DATASET = 'lineas-diurnas-de-autobuses-bilbobus';

    const RESOURCE = '5d82c6c5-474d-41d4-8986-7bc7654d4c3b';

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
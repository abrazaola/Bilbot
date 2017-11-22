<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class BusStopsController extends FOSRestController
{
    const DATASET = 'paradas-de-autobuses-bilbobus';

    const RESOURCE = '12b7095c-eda3-48cc-b71c-dd750bd27b91';

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
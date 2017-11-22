<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class TouristOfficesController extends FOSRestController
{
    const DATASET = 'oficinas-de-turismo-de-euskadi';

    const RESOURCE = '94537200-5d08-44ce-a366-c6075ddff6a2';

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
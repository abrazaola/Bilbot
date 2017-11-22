<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class TouristAttractionsController extends FOSRestController
{
    const DATASET = 'lugares-de-interes-turistico';

    const RESOURCE = '78586fd4-a285-4234-b448-88f3c86938ee';

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
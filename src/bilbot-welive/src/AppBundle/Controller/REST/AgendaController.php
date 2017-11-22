<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class AgendaController extends FOSRestController
{
    const DATASET = 'agenda-municipal-de-bilbao';

    const RESOURCE = '78505965-dd1b-46f3-8d81-445ac7bd364f';

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
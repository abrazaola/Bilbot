<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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

    public function searchAction(Request $request) {
        $keyword = $request->query->get('term');

        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, NOMBRE_FAMILIA, NOMBRE_LUGAR_CAS, NOMBRE_LUGAR_EUS, NOMBRE_CALLE
                  from results 
                  where 
                  NOMBRE_FAMILIA LIKE '%".$keyword."%' OR
                  NOMBRE_LUGAR_CAS LIKE '%".$keyword."%' OR
                  NOMBRE_CALLE LIKE '%".$keyword."%' OR
                  NOMBRE_LUGAR_EUS LIKE '%".$keyword."%';"
            );

        return new JsonResponse($res, 200);
    }

    public function listAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, NOMBRE_FAMILIA, NOMBRE_LUGAR_CAS 
                  from results limit 10;"
            );

        return new JsonResponse($res, 200);
    }

    public function detailAction(Request $request) {
        $attractionId = $request->query->get('_id');

        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, NOMBRE_FAMILIA, NOMBRE_LUGAR_CAS, NOMBRE_TIPO_VIA, NOMBRE_CALLE, BLOQUE, NUMERO, BIS, COORDENADA_UTM_X, COORDENADA_UTM_Y 
                  from results 
                  where 
                  _id = " . $attractionId . ";"
            );

        return new JsonResponse($res, 200);
    }
}
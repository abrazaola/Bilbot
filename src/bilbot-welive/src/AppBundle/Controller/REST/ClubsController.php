<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ClubsController extends FOSRestController
{
    const DATASET = 'guia-de-asociaciones-de-bilbao';

    const RESOURCE = '2fb58eaa-3815-4b5f-bce7-91b9ec827640';

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

    public function listAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                'select _id, Nombre from results limit 10;'
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
                "select _id, Nombre, Actividades, `Sector de población`, `Ámbito de actuación` 
                  from results 
                  where 
                  Nombre LIKE '%".$keyword."%' OR 
                  `Sector de población` LIKE '%".$keyword."%' OR 
                  `Ámbito de actuación` LIKE '%".$keyword."%' OR 
                  Actividades LIKE '%".$keyword."%'
                  limit 10;"
            );

        return new JsonResponse($res, 200);
    }

    public function detailAction(Request $request) {
        $id = $request->query->get('_id');

        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, Nombre, Dirección, Teléfono, Email, Actividades 
                  from results 
                  where 
                  _id = ".$id.";"
            );

        return new JsonResponse($res, 200);
    }
}
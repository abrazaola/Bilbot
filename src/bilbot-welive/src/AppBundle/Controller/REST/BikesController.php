<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BikesController extends FOSRestController
{
    const DATASET = 'puntos-de-recogida-de-bicicletas';

    const RESOURCE = '09bfb791-aca7-4762-855a-fd17ccae15f0';

    const NEAR_METERS = 500;

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
                "select _id, ALIBRES, BLIBRES, NOMBRE, LATITUD, LONGITUD 
                  from results 
                  where 
                  NOMBRE LIKE '%".$keyword."%' AND 
                  (ALIBRES > 0 OR 
                  BLIBRES > 0);"
            );

        return new JsonResponse($res, 200);
    }

    public function nearPointAction(Request $request) {
        $coordX = $request->query->get('coordX');
        $coordY = $request->query->get('coordY');
        $calculator = $this->get('distance_calculator_between_points');
        $nearPoints = [];

        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, ALIBRES, BLIBRES, NOMBRE, LATITUD, LONGITUD 
                  from results
                  where 
                  (ALIBRES > 0 OR 
                  BLIBRES > 0);"
            );

        foreach ($res['rows'] as $row) {
            $meters = $calculator->getMetersBetweenPoints($coordY, $coordX, $row['LATITUD'], $row['LONGITUD']);

            if ($meters < self::NEAR_METERS) {
                $nearPoints[] = $row;
            }
        }

        return new JsonResponse($nearPoints, 200);
    }

    public function listAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, ALIBRES, BLIBRES, NOMBRE, LATITUD, LONGITUD 
                  from results
                  where 
                  (ALIBRES > 0 OR 
                  BLIBRES > 0);"
            );

        return new JsonResponse($res, 200);
    }

    public function detailAction(Request $request) {
        $bikePointId = $request->query->get('_id');

        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, ALIBRES, BLIBRES, NOMBRE, LATITUD, LONGITUD 
                  from results 
                  where 
                  _id = ".$bikePointId.";"
            );

        return new JsonResponse($res, 200);
    }
}
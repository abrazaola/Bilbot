<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
                "select * from rootTable  WHERE
                  municipality = 'BILBAO'
                  limit 10;"
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
                "select _id, documentName, turismDescription, lodgingType, placename, templateType 
                  from rootTable 
                  where 
                  documentName LIKE '%".$keyword."%' OR
                  turismDescription LIKE '%".$keyword."%' OR
                  lodgingType LIKE '%".$keyword."%' OR
                  placename LIKE '%".$keyword."%' AND
                  municipality = 'BILBAO';"
            );

        return new JsonResponse($res, 200);
    }
/*
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
            $meters = $calculator->getMetersBetweenPoints($coordX, $coordY, $row['LATITUD'], $row['LONGITUD']);

            if ($meters < self::NEAR_METERS) {
                $nearPoints[] = $row;
            }
        }

        return new JsonResponse($nearPoints, 200);
    }
*/
    public function listAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, documentName, municipality, qualityQ, qualityAssurance, lodgingType 
                  from rootTable
                  where 
                  municipality = 'BILBAO' AND 
                  (qualityQ = '1' OR 
                  qualityAssurance = '1')
                  limit 10;"
            );

        return new JsonResponse($res, 200);
    }

    public function detailAction(Request $request) {
        $hotelId = $request->query->get('_id');

        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, documentName, turismDescription, accesibility, email, web, lodgingType, phoneNumber, qualityQ, latitudelongitude, friendlyUrl 
                  from rootTable 
                  where 
                  _id = ".$hotelId.";"
            );

        return new JsonResponse($res, 200);
    }
}
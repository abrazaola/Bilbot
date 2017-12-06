<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RestaurantsController extends FOSRestController
{
    const DATASET = 'restaurante-sidrerias-y-bodegas-de-euskadi1';

    const RESOURCE = 'cb835659-1d74-4131-82dd-35fa6d88a511';

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

    public function searchAction(Request $request) {
        $keyword = $request->query->get('term');

        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, documentName, documentDescription, restorationType, municipality 
                  from rootTable 
                  where 
                  (documentName LIKE '%".$keyword."%' OR
                  documentDescription LIKE '%".$keyword."%' OR
                  restorationType LIKE '%".$keyword."%') AND
                  municipality = 'BILBAO';"
            );

        return new JsonResponse($res, 200);
    }

    public function listAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, documentName, municipality, restorationType, michelinStar 
                  from rootTable 
                  where 
                  municipality = 'BILBAO' AND 
                  michelinStar = '1';"
            );

        return new JsonResponse($res, 200);
    }

    public function detailAction(Request $request) {
        $restaurantId = $request->query->get('_id');

        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, documentName, documentDescription, accesibility, email, michelinStar, phoneNumber, recomended, restorationType, latitudelongitude, friendlyUrl 
                  from rootTable 
                  where 
                  municipality = 'BILBAO' AND 
                  _id = " . $restaurantId . ";"
            );

        return new JsonResponse($res, 200);
    }
}
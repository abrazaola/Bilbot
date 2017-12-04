<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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

    public function searchAction(Request $request) {
        $keyword = $request->query->get('term');

        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, municipality, documentName, documentDescription
                  from rootTable 
                  where 
                  (documentDescription LIKE '%".$keyword."%' OR
                  documentName LIKE '%".$keyword."%') AND
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
                "select _id, municipality, documentName, documentDescription 
                  from rootTable where municipality = 'BILBAO' limit 10;"
            );

        return new JsonResponse($res, 200);
    }

    public function detailAction(Request $request) {
        $officeId = $request->query->get('_id');

        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select _id, municipality, documentName, documentDescription, latitudelongitude, email, phoneNumber, friendlyUrl 
                  from rootTable 
                  where 
                  _id = " . $officeId . ";"
            );

        return new JsonResponse($res, 200);
    }
}
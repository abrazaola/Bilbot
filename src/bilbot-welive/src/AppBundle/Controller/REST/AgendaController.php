<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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

        return new JsonResponse($res, 200);
    }

    public function searchAction(Request $request) {
        $keyword = $request->query->get('term');

        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select titulo, hora, lugar, direccion, tipo, fecha_hasta, info 
                  from rootTable 
                  where 
                  titulo LIKE '%".$keyword."%' OR 
                  lugar LIKE '%".$keyword."%' AND 
                  DATE(fecha_hasta) > '".date('Y-m-d H:i:s')."' AND
                  DATE(fecha_desde) < '".date('Y-m-d H:i:s')."';"
            );

        return new JsonResponse($res, 200);
    }

    public function listAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select titulo, hora, lugar, direccion, tipo, fecha_hasta, fecha_desde, info 
                  from rootTable 
                  where 
                  DATE(fecha_hasta) > '".date('Y-m-d H:i:s')."' AND
                  DATE(fecha_desde) < '".date('Y-m-d H:i:s')."';"
            );

        return new JsonResponse($res, 200);
    }

    public function detailAction(Request $request) {
        $eventTitle = $request->query->get('title');

        $res = $this
            ->get('welive_api_consumer')
            ->query(
                self::DATASET,
                self::RESOURCE,
                "select titulo, hora, lugar, direccion, tipo, fecha_hasta, fecha_desde, info 
                  from rootTable 
                  where 
                  titulo LIKE '".$eventTitle."%';"
            );

        return new JsonResponse($res, 200);
    }
}
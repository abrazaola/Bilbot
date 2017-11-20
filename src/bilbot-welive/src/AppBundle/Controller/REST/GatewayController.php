<?php

namespace AppBundle\Controller\REST;


use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

class GatewayController extends FOSRestController
{
    public function clubsAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                'guia-de-asociaciones-de-bilbao',
                '2fb58eaa-3815-4b5f-bce7-91b9ec827640',
                'select * from results limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }

    public function agendaAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                'agenda-municipal-de-bilbao',
                '78505965-dd1b-46f3-8d81-445ac7bd364f',
                'select * from rootTable limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }

    public function restaurantsAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                'restaurante-sidrerias-y-bodegas-de-euskadi1',
                'cb835659-1d74-4131-82dd-35fa6d88a511',
                'select * from rootTable limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }

    public function bikesAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                'puntos-de-recogida-de-bicicletas',
                '09bfb791-aca7-4762-855a-fd17ccae15f0',
                'select * from results limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }

    public function busesPerRouteAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                'paradas-de-autobuses-bilbobus-por-recorrido-de-las-lineas',
                '420ced04-7552-4e27-85d8-8d06484c3e37',
                'select * from results limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }

    public function busStopsAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                'paradas-de-autobuses-bilbobus',
                '12b7095c-eda3-48cc-b71c-dd750bd27b91',
                'select * from results limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }

    public function nightlyBusRoutesAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                'lineas-nocturnas-de-autobuses-bilbobus',
                '90d70b5f-197c-4291-ab88-4b16e8fe86f3',
                'select * from results limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }

    public function dailyBusRoutesAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                'lineas-diurnas-de-autobuses-bilbobus',
                '5d82c6c5-474d-41d4-8986-7bc7654d4c3b',
                'select * from results limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }

    public function touristOfficesAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                'oficinas-de-turismo-de-euskadi',
                '94537200-5d08-44ce-a366-c6075ddff6a2',
                'select * from rootTable limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }

    public function touristAttractionsAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                'lugares-de-interes-turistico',
                '78586fd4-a285-4234-b448-88f3c86938ee',
                'select * from results limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }

    public function hotelsAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                'alojamientos-turisticos-de-euskadi',
                '78a18256-cfba-4f51-825f-7e2e0f48b822',
                'select * from rootTable limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }

    public function portalsAction() {
        $res = $this
            ->get('welive_api_consumer')
            ->query(
                'portales-de-bilbao',
                '73b6103b-0c12-4b2c-98ae-71ed33e55e8c',
                'select * from results limit 10;'
            );

        $data = [
            'results'    => $res,
        ];

        return new JsonResponse($data, 200);
    }
}
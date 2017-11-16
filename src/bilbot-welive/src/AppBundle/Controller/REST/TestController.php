<?php

namespace AppBundle\Controller\REST;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TestController extends FOSRestController
{
    public function executeAction(Request $request) {
        $name = $request->query->get('name');

        if (!empty($name) ) {
            return new JsonResponse([
                'text' => 'Hello ' . $name . '!!!'
            ], 200);
        }

        return new JsonResponse([
            'error' => 'Empty name'
        ], 300);
    }
}
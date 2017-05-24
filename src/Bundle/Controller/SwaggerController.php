<?php

namespace PhpSolution\SwaggerUIGen\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class SwaggerController
 */
class SwaggerController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function dataAction(): JsonResponse
    {
        $configProvider = $this->get('swagger_uigen.data_provider');
        $schema = $this->get('swagger_uigen.swagger_provider')->getSwaggerData($configProvider);

        return new JsonResponse($schema, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @return Response
     */
    public function dumpAction(): Response
    {
        $configProvider = $this->get('swagger_uigen.data_provider');
        $schema = $this->get('swagger_uigen.swagger_provider')->getSwaggerData($configProvider);

        return new Response(VarDumper::dump($schema));
    }
}
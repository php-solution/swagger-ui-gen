<?php

namespace PhpSolution\SwaggerUIGen\Bundle\Controller;

use PhpSolution\SwaggerUIGen\Component\DataProvider\DataProviderInterface;
use PhpSolution\SwaggerUIGen\Component\SwaggerProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\VarDumper;

/**
 * SwaggerController
 */
class SwaggerController extends AbstractController
{
    /**
     * @param DataProviderInterface $configProvider
     * @param SwaggerProvider       $swaggerProvider
     *
     * @return JsonResponse
     */
    public function dataAction(DataProviderInterface $configProvider, SwaggerProvider $swaggerProvider): JsonResponse
    {
        $schema = $swaggerProvider->getSwaggerData($configProvider);

        return new JsonResponse($schema, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @param DataProviderInterface $configProvider
     * @param SwaggerProvider       $swaggerProvider
     *
     * @return Response
     */
    public function dumpAction(DataProviderInterface $configProvider, SwaggerProvider $swaggerProvider): Response
    {
        $schema = $swaggerProvider->getSwaggerData($configProvider);
        class_exists('Symfony\Component\VarDumper\VarDumper')
            ? $responseData = VarDumper::dump($schema)
            : $responseData = '<pre>' . print_r($schema, true);

        return new Response($responseData);
    }
}

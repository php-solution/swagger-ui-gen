<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Swagger;

use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\PathItemFactory;
use PhpSolution\SwaggerUIGen\Component\DataNormalizer\DataNormalizerInterface;
use PhpSolution\SwaggerUIGen\Component\Model\Swagger;
use PhpSolution\SwaggerUIGen\Component\ModelHandler\SwaggerBuilderInterface;

/**
 * Class RoutePaths
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Swagger
 */
class RoutePaths implements SwaggerBuilderInterface, DataNormalizerInterface
{
    /**
     * @var PathItemFactory
     */
    private $pathItemFactory;

    /**
     * RoutePathBuilder constructor.
     *
     * @param PathItemFactory $objectsFactory
     */
    public function __construct(PathItemFactory $objectsFactory)
    {
        $this->pathItemFactory = $objectsFactory;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public function normalize(array $config): array
    {
        return isset($config['sf_route_paths']) ? ['sf_route_paths' => $config['sf_route_paths']] : [];
    }

    /**
     * @param Swagger $swagger
     * @param array   $configs
     */
    public function build(Swagger $swagger, array $configs): void
    {
        foreach ($configs['sf_route_paths'] ?? [] as $config) {
            $this->pathItemFactory->createPathItemObject($config, $swagger);
        }
    }
}
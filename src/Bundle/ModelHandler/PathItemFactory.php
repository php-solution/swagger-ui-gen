<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler;

use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\OperationBuilderInterface;
use PhpSolution\SwaggerUIGen\Component\Model\OpenAPI;
use PhpSolution\SwaggerUIGen\Component\ModelHandler\GeneralFactory;
use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use PhpSolution\SwaggerUIGen\Component\Model\Parameter;
use PhpSolution\SwaggerUIGen\Component\Model\ParameterGeneralInfo;
use PhpSolution\SwaggerUIGen\Component\Model\PathItem;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class PathItemFactory
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\ModelHandler
 */
class PathItemFactory
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var GeneralFactory
     */
    private $openapiObjectFactory;
    /**
     * @var OperationBuilderInterface[]|array
     */
    private $operationBuilders = [];

    /**
     * PathItemFactory constructor.
     *
     * @param RouterInterface $router
     * @param GeneralFactory  $generalFactory
     */
    public function __construct(RouterInterface $router, GeneralFactory $generalFactory)
    {
        $this->router = $router;
        $this->openapiObjectFactory = $generalFactory;
    }

    /**
     * @param OperationBuilderInterface $builder
     */
    public function addOperationBuilder(OperationBuilderInterface $builder): void
    {
        $this->operationBuilders[] = $builder;
    }

    /**
     * @param array $config
     *
     * @return PathItem
     */
    public function createPathItemObject(array $config, OpenAPI $swagger): PathItem
    {
        if (is_null($routeInfo = $this->router->getRouteCollection()->get($config['route']))) {
            throw new \InvalidArgumentException(sprintf('Undefined route name "%s"', $config['route']));
        };

        $routePath = $routeInfo->getPath();
        $pathItem = $swagger->hasPathItem($routePath)
            ? $swagger->getPathItem($routePath)
            : $this->openapiObjectFactory->createPathItemObject($config['openapi_params'] ?? []);

        $config['schemes'] = $routeInfo->getSchemes();
        $parameters = $this->createParametersFromRoute($routeInfo);
        foreach ($config['methods'] ?? $routeInfo->getMethods() as $method) {
            $method = strtolower($method);
            if ($pathItem->hasOperation($method)) {
                $operation = $pathItem->getOperation($method);
            } elseif (array_key_exists('openapi_params', $config) && array_key_exists($method, $config['openapi_params'])) {
                $operation = $this->openapiObjectFactory->createOperationObject($config['openapi_params'][$method]);
            } else {
                $operation = new Operation();
            }
            $operation->mergeToParameters($parameters);
            foreach ($this->operationBuilders as $operationBuilder) {
                $operationBuilder->build($operation, $config);
            }
            $pathItem->addOperation($method, $operation);
        }

        $swagger->addPathItem($routePath, $pathItem);

        return $pathItem;
    }

    /**
     * @param Route $route
     *
     * @return array|Parameter[]
     */
    private function createParametersFromRoute(Route $route): array
    {
        $result = [];
        $routeDefaults = $route->getDefaults();
        foreach ($route->getDefaults() as $key => $value) {
            if ($key === '_controller') {
                continue;
            }
            $generalInfo = new ParameterGeneralInfo();
            $generalInfo->setTypeByVariable($value);
            $generalInfo->setDefault($value);
            $parameter = new Parameter(Parameter::IN_QUERY, $key);
            $parameter->setGeneralInfo($generalInfo);
            $result[$key] = $parameter;
        }

        $routeRequirements = $route->getRequirements();
        $routeCompile = $route->compile();
        $routePathVars = $routeCompile->getPathVariables();
        foreach ($routeCompile->getVariables() as $name) {
            $generalInfo = new ParameterGeneralInfo();
            $generalInfo->setType('string');
            if (array_key_exists($name, $routeDefaults)) {
                $generalInfo->setDefault($routeDefaults[$name]);
            }
            $generalInfo->setPattern($routeRequirements[$name] ?? null);

            $in = in_array($name, $routePathVars) ? Parameter::IN_PATH : Parameter::IN_QUERY;
            $parameter = new Parameter($in, $name);
            $parameter->setRequired(true);
            $parameter->setGeneralInfo($generalInfo);
            if (array_key_exists($name, $routeRequirements)) {
                $parameter->addDescription('<b>Requirements:</b> '.$routeRequirements[$name]);
            }
            $result[$name] = $parameter;
        }

        return array_values($result);
    }
}

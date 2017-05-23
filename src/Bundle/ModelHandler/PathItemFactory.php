<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler;

use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\OperationBuilderInterface;
use PhpSolution\SwaggerUIGen\Component\Model\Swagger;
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
    public function createPathItemObject(array $config, Swagger $swagger): PathItem
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
            $operation = $pathItem->getOperation($method) ?: new Operation();
            $operation->setParameters($parameters);
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
        foreach ($route->getDefaults() as $key => $value) {
            if ($key === '_controller') {
                continue;
            }
            $generalInfo = new ParameterGeneralInfo();
            $generalInfo->setTypeByVariable($value);
            $parameter = new Parameter();
            $parameter->setName($key);
            $parameter->setIn(Parameter::IN_QUERY);
            $parameter->setGeneralInfo($generalInfo);
            $result[] = $parameter;
        }

        $routeRequirements = $route->getRequirements();
        foreach ($route->compile()->getVariables() as $name) {
            $generalInfo = new ParameterGeneralInfo();
            $generalInfo->setType('string');
            $generalInfo->setPattern($routeRequirements[$name] ?? null);

            $parameter = new Parameter();
            $parameter->setName($name);
            $parameter->setIn(Parameter::IN_QUERY);
            $parameter->setRequired(true);
            $parameter->setGeneralInfo($generalInfo);
            $result[] = $parameter;
        }

        return $result;
    }
}
<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation;

use Doctrine\Common\Annotations\Reader;
use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\SchemaFactory;
use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use PhpSolution\SwaggerUIGen\Component\Model\Response;
use PhpSolution\SwaggerUIGen\Component\ModelHandler\GeneralFactory;
use Symfony\Component\Routing\RouterInterface;

/**
 * GeneralBuilder
 */
class GeneralBuilder implements OperationBuilderInterface
{
    /**
     * @var GeneralFactory
     */
    private $openapiObjectFactory;
    /**
     * @var SchemaFactory
     */
    private $schemaModelFactory;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param GeneralFactory  $generalFactory
     * @param SchemaFactory   $schemaFactory
     * @param RouterInterface $router
     * @param Reader          $reader
     */
    public function __construct(GeneralFactory $generalFactory, SchemaFactory $schemaFactory, RouterInterface $router, Reader $reader)
    {
        $this->openapiObjectFactory = $generalFactory;
        $this->schemaModelFactory = $schemaFactory;
        $this->router = $router;
        $this->reader = $reader;
    }

    /**
     * @param Operation $operation
     * @param array     $config
     */
    public function build(Operation $operation, array $config): void
    {
        foreach (['schemes', 'tags', 'description', 'externalDocs'] as $fieldName) {
            if (array_key_exists($fieldName, $config) && $config[$fieldName]) {
                $operation->{'set' . ucfirst($fieldName)}($config[$fieldName]);
            }
        }
        if (array_key_exists('security', $config) && $config['security']) {
            foreach ($config['security'] ?? [] as $securityConfigItem) {
                $security = $this->openapiObjectFactory->createSecurityRequirement($securityConfigItem);
                $operation->addSecurity($security);
            }
        }
        if (array_key_exists('parameters', $config) && $config['parameters']) {
            foreach ($config['parameters'] ?? [] as $parameterConfigItem) {
                $parameter = $this->openapiObjectFactory->createParameterObject($parameterConfigItem);
                $operation->addParameter($parameter);
            }
        }
        $response = $this->createResponse($config['response'] ?? []);
        $operation->addResponse($config['response']['status_code'] ?? 'default', $response);
        $operation->addDescription($this->getSecurityAnnotation($config));
    }

    /**
     * @param array $config
     *
     * @return string
     */
    private function getSecurityAnnotation(array $config): string
    {
        $expression = '';

        $route = $this->router->getRouteCollection()->get($config['route']);
        $method = $this->getReflectionMethod($route->getDefault('_controller'));

        if ($method) {
            $annotation = $this->reader->getMethodAnnotation($method, 'Sensio\Bundle\FrameworkExtraBundle\Configuration\Security');

            if (!$annotation) {
                $reflectionClass = new \ReflectionClass($method->getDeclaringClass()->getName());
                $classAnnotation = $this->reader->getClassAnnotation($reflectionClass, 'Sensio\Bundle\FrameworkExtraBundle\Configuration\Security');

                if ($classAnnotation) {
                    $expression = '<b>Security:</b> ' . $classAnnotation->getExpression();
                }
            } else {
                $expression = '<b>Security:</b> ' . $annotation->getExpression();
            }
        }

        return $expression;
    }

    /**
     * Returns the ReflectionMethod for the given controller string.
     *
     * @param string $controller
     * @return \ReflectionMethod|null
     */
    private function getReflectionMethod($controller)
    {
        if (preg_match('#(.+)::([\w]+)#', $controller, $matches)) {
            $class = $matches[1];
            $method = $matches[2];
        }

        if (isset($class) && isset($method)) {
            try {
                return new \ReflectionMethod($class, $method);
            } catch (\ReflectionException $e) {
            }
        }

        return null;
    }

    /**
     * @param array $config
     *
     * @return Response
     */
    private function createResponse(array $config): Response
    {
        $responseSchema = $this->schemaModelFactory->createSchemaObject($config);
        $response = $this->openapiObjectFactory->createResponseObject([]);
        $response->setSchema($responseSchema);

        return $response;
    }
}
<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\AnnotationBuilder;

use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema\PhpDoc\SimpleAnnotationParser;
use PhpSolution\SwaggerUIGen\Component\Model\Items;
use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use PhpSolution\SwaggerUIGen\Component\Model\Parameter;
use PhpSolution\SwaggerUIGen\Component\Model\ParameterGeneralInfo;

/**
 * ParametersBuilder
 */
class ParametersBuilder
{
    /**
     * @var Operation
     */
    private $operation;

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $annotationName;

    /**
     * @var SimpleAnnotationParser
     */
    private $annotationParser;

    /**
     * @param Operation $operation
     * @param string    $className
     * @param string    $annotationName
     */
    public function __construct(Operation $operation, string $className, string $annotationName = 'api')
    {
        $this->operation = $operation;
        $this->className = $className;
        $this->annotationName = $annotationName;
        $this->annotationParser = new SimpleAnnotationParser();
    }

    /**
     * @return void
     */
    public function build(): void
    {
        $this->buildClass($this->className, null);
    }

    /**
     * @param string      $className
     * @param null|string $prefix
     */
    private function buildClass(string $className, ?string $prefix)
    {
        $refClass = new \ReflectionClass($className);
        foreach ($refClass->getProperties() as $refProp) {
            $annotations = $this->annotationParser->getAnnotations($refProp->getDocComment());
            if (!array_key_exists($this->annotationName, $annotations)) {
                continue;
            }

            $name = $this->createName($refProp->getName(), $prefix);
            $this->addParameter($name, $annotations['api']);
        }
    }

    /**
     * @param string      $property
     * @param null|string $prefix
     *
     * @return string
     */
    private function createName(string $property, ?string $prefix): string
    {
        $property = $this->normalizeProperty($property);

        return null === $prefix ? $property : sprintf('%s[%s]', $prefix, $property);
    }

    /**
     * @param string $property
     *
     * @return string
     */
    private function normalizeProperty(string $property): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_\\0', $property));
    }

    /**
     * @param string $name
     * @param array  $annotation
     *
     * @return void
     */
    private function addParameter(string $name, array $annotation): void
    {
        if (class_exists($annotation['type'])) {
            $this->buildClass($annotation['type'], $name);
        } else {
            $info = new ParameterGeneralInfo();
            $info->setType($annotation['type']);
            $info->setCollectionFormat($annotation['collectionFormat'] ?? 'multi');
            if (array_key_exists('items', $annotation)) {
                $info->setItems(new Items($annotation['items']));
            }

            $parameter = new Parameter(Parameter::IN_QUERY);
            $parameter->setName($name . ('array' === $annotation['type'] ? '[]' : ''));
            $parameter->setRequired($annotation['required'] ?? false);
            $parameter->setDescription($annotation['description'] ?? '');
            $parameter->setExample($annotation['example'] ?? '');
            $parameter->setGeneralInfo($info);

            $this->operation->addParameter($parameter);
        }
    }
}

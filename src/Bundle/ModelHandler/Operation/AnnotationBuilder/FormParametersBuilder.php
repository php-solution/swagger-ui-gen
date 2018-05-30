<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\AnnotationBuilder;

use PhpSolution\SwaggerUIGen\Component\Model\Items;
use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use PhpSolution\SwaggerUIGen\Component\Model\Parameter;
use PhpSolution\SwaggerUIGen\Component\Model\ParameterGeneralInfo;

/**
 * FormParametersBuilder
 */
class FormParametersBuilder extends AbstractParametersBuilder
{
    /**
     * @param Operation $operation
     * @param string    $className
     */
    public function build(Operation $operation, string $className): void
    {
        $this->buildClass($operation, $className, null);
    }

    /**
     * @param Operation   $operation
     * @param string      $className
     * @param null|string $prefix
     */
    private function buildClass(Operation $operation, string $className, ?string $prefix): void
    {
        $validationHandler = new ValidationHandler($this->classMetadataFactory->getMetadataFor($className));
        foreach ($this->getReflectionClass($className)->getProperties() as $refProp) {
            $annotations = $this->annotationParser->getAnnotations($refProp->getDocComment());
            if (!array_key_exists($this->annotationName, $annotations)) {
                continue;
            }

            $name = $this->createName($refProp->getName(), $prefix);
            $annotation = $annotations[$this->annotationName];

            if (class_exists($annotation['type'])) {
                $this->buildClass($operation, $annotation['type'], $name);
            } else {
                $parameter = $this->createParameter($name, $annotation);
                $validationHandler->handleParameter($parameter, $name);
                $operation->addParameter($parameter);
            }
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
        $property = $this->normalizePropertyName($property);

        return null === $prefix ? $property : sprintf('%s[%s]', $prefix, $property);
    }

    /**
     * @param string $name
     * @param array  $annotation
     *
     * @return Parameter
     */
    private function createParameter(string $name, array $annotation): Parameter
    {
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

        return $parameter;
    }
}

<?php
declare(strict_types=1);

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\AnnotationBuilder;

use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use PhpSolution\SwaggerUIGen\Component\Model\Parameter;
use PhpSolution\SwaggerUIGen\Component\Model\Schema;

/**
 * BodyParametersBuilder
 */
class BodyParametersBuilder extends AbstractParametersBuilder
{
    private const CONSUMES_JSON = 'application/json';

    /**
     * @param Operation $operation
     * @param string    $className
     */
    public function build(Operation $operation, string $className): void
    {
        $operation->setConsumes([self::CONSUMES_JSON]);
        $parameter = new Parameter(Parameter::IN_BODY);
        $operation->addParameter($parameter);

        $parameter->setName(Parameter::IN_BODY);
        $parameter->setRequired(true);
        $parameter->setSchema($this->buildClass($className));
    }

    /**
     * @param string $className
     *
     * @return Schema
     */
    private function buildClass(string $className): Schema
    {
        $schema = new Schema('object');
        foreach ($this->getReflectionClass($className)->getProperties() as $refProp) {
            $annotations = $this->annotationParser->getAnnotations($refProp->getDocComment());
            if (!array_key_exists($this->annotationName, $annotations)) {
                continue;
            }

            $name = $this->normalizePropertyName($refProp->getName());
            $property = $this->buildBodyProperty($annotations[$this->annotationName]);
            $schema->addProperty($name, $property);
        }

        return $schema;
    }

    /**
     * @param array $annotation
     *
     * @return Schema
     */
    private function buildBodyProperty(array $annotation): Schema
    {
        if (class_exists($annotation['type'])) {
            $schema = $this->buildClass($annotation['type']);
        } else {
            $schema = new Schema($annotation['type'] ?? 'string');
            $schema->setRef($annotation['ref'] ?? null);
            $schema->setExample($annotation['example'] ?? null);
            $schema->setDescription($annotation['description'] ?? null);
            $schema->setMaximum($annotation['max'] ?? null);
            $schema->setMinimum($annotation['min'] ?? null);
            $schema->setRequired($annotation['required'] ?? false);
            if (!empty($annotation['enum'])) {
                $schema->setEnum($annotation['enum']);
            }
        }

        return $schema;
    }
}

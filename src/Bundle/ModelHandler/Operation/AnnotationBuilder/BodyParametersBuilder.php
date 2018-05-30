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
        $parameter->setName(Parameter::IN_BODY);
        $parameter->setRequired(true);

        $schema = new Schema('object');
        $parameter->setSchema($schema);
        $operation->addParameter($parameter);

        foreach ($this->getReflectionClass($className)->getProperties() as $refProp) {
            $annotations = $this->annotationParser->getAnnotations($refProp->getDocComment());
            if (!array_key_exists($this->annotationName, $annotations)) {
                continue;
            }

            $name = $this->normalizePropertyName($refProp->getName());
            $property = $this->buildBodyProperty($annotations[$this->annotationName]);
            $schema->addProperty($name, $property);
        }
    }

    /**
     * @param array $apiData
     *
     * @return Schema
     */
    private function buildBodyProperty(array $apiData): Schema
    {
        $schema = new Schema($apiData['type'] ?? 'string');
        $schema->setExample($apiData['example'] ?? null);
        $schema->setDescription($apiData['description'] ?? null);
        $schema->setMaximum($apiData['max'] ?? null);
        $schema->setMinimum($apiData['min'] ?? null);
        $schema->setRequired($apiData['required'] ?? false);
        if (!empty($apiData['enum'])) {
            $schema->setEnum($apiData['enum']);
        }

        return $schema;
    }
}

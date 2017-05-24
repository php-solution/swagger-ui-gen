<?php

namespace PhpSolution\SwaggerUIGen\Component;

use PhpSolution\SwaggerUIGen\Component\Model;
use PhpSolution\SwaggerUIGen\Component\Utils\ObjectExtractConfig;
use PhpSolution\SwaggerUIGen\Component\Utils\ObjectExtractor;

/**
 * Class SwaggerModelExtractor
 *
 * @package PhpSolution\SwaggerUIGen\Component\Utils
 */
class SwaggerModelExtractor
{
    /**
     * @var ObjectExtractor
     */
    private $dataExtractor;

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object): array
    {
        if (!$this->dataExtractor instanceof ObjectExtractor) {
            $dataExtractor = new ObjectExtractor();
            foreach ($this->getExtractConfigBuilders() as $class => $configBuilder) {
                $config = new ObjectExtractConfig($class);
                $configBuilder($config);
                $dataExtractor->addExtractorConfig($class, $config);
            }
            $this->dataExtractor = $dataExtractor;
        }

        return $this->dataExtractor->extract($object);
    }

    /**
     * @return array
     */
    private function getExtractConfigBuilders(): array
    {
        return [
            Model\Swagger::class => function (ObjectExtractConfig $config) {
                $config->setProps(['swagger', 'host', 'basePath', 'info', 'externalDocs']);
                $config->setCollectionsProps(['paths', 'definitions', 'parameters', 'responses', 'securityDefinitions', 'security', 'tags']);
            },
            Model\Items::class => function (ObjectExtractConfig $config) {
                $config->setIgnoredProps(['extensionsFields']);
            },
            Model\Operation::class => function (ObjectExtractConfig $config) {
                $config->setCollectionsProps(['parameters', 'security', 'responses']);
            },
            Model\Response::class => function (ObjectExtractConfig $config) {
                $config->setProps(['schema', 'description', 'examples']);
                $config->setCollectionsProps(['headers']);
            },
            Model\Schema::class => function (ObjectExtractConfig $config) {
                $config->setCollectionsProps(['additionalProperties', 'allOf', 'properties']);
                $config->setIgnoredProps(['parent']);
                $config->setNameTransformer(['ref' => '$ref']);
            },
            Model\PathItem::class => function (ObjectExtractConfig $config) {
                $config->setCustomExtractor(
                    function (Model\PathItem $pathItem, ObjectExtractor $extractor) {
                        $result = [];
                        if ($pathItem->getRef()) {
                            $result['$ref'] = $pathItem->getRef();
                        }
                        foreach ($pathItem->getOperations() as $name => $operation) {
                            $result[$name] = $extractor->extract($operation);
                        }
                        $result['parameters'] = [];
                        foreach ($pathItem->getParameters() as $parameter) {
                            $result['parameters'][] = $extractor->extract($parameter);
                        }

                        return $result;
                    }
                );
            },
            Model\Parameter::class => function (ObjectExtractConfig $config) {
                $config->setCustomExtractor(
                    function (Model\Parameter $parameter, ObjectExtractor $extractor) {
                        $result = [
                            'in' => $parameter->getIn(),
                            'name' => $parameter->getName(),
                            'description' => $parameter->getDescription(),
                            'required' => $parameter->isRequired(),
                        ];
                        if (($generalInfo = $parameter->getGeneralInfo()) instanceof Model\ParameterGeneralInfo) {
                            $result = array_merge($result, $extractor->extract($generalInfo));
                        }
                        if (($schema = $parameter->getSchema()) instanceof Model\Schema) {
                            $result = array_merge($result, $extractor->extract($schema));
                        }

                        return $result;
                    }
                );
            },
            Model\Example::class => function (ObjectExtractConfig $config) {
                $config->setCustomExtractor(
                    function (Model\Example $example, ObjectExtractor $extractor) {
                        return $example->getEls();
                    }
                );
            },
            Model\SecurityRequirement::class => function (ObjectExtractConfig $config) {
                $config->setCustomExtractor(
                    function (Model\SecurityRequirement $object) {
                        return $object->getFields();
                    }
                );
            },
        ];
    }
}
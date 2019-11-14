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
            Model\OpenAPI::class => static function (ObjectExtractConfig $config) {
                $config->setProps(['openapi', 'servers', 'info', 'components', 'externalDocs']);
                $config->setCollectionsProps(['servers', 'paths', 'parameters', 'responses', 'security', 'tags']);
            },
            Model\Components::class => static function(ObjectExtractConfig $config) {
                $config->setProps(['securitySchemes', 'schemas']);
                $config->setCollectionsProps(['securitySchemes', 'schemas']);
            },
            Model\Items::class => static function (ObjectExtractConfig $config) {
                $config->setIgnoredProps(['extensionsFields']);
            },
            Model\Operation::class => static function (ObjectExtractConfig $config) {
                $config->setCollectionsProps(['parameters', 'security', 'responses']);
            },
            Model\Response::class => static function (ObjectExtractConfig $config) {
                $config->setProps(['schema', 'description', 'examples']);
                $config->setCollectionsProps(['headers']);
            },
            Model\Schema::class => static function (ObjectExtractConfig $config) {
                $config->setCollectionsProps(['additionalProperties', 'allOf', 'properties']);
                $config->setIgnoredProps(['parent']);
                $config->setNameTransformer(['ref' => '$ref']);
            },
            Model\PathItem::class => static function (ObjectExtractConfig $config) {
                $config->setCustomExtractor(
                    static function (Model\PathItem $pathItem, ObjectExtractor $extractor) {
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
            Model\Parameter::class => static function (ObjectExtractConfig $config) {
                $config->setCustomExtractor(
                    static function (Model\Parameter $parameter, ObjectExtractor $extractor) {
                        $result = [
                            'in' => $parameter->getIn(),
                            'name' => $parameter->getName(),
                            'description' => $parameter->getDescription(),
                            'required' => $parameter->isRequired(),
                            'example' => $parameter->getExample()
                        ];
                        if (($generalInfo = $parameter->getGeneralInfo()) instanceof Model\ParameterGeneralInfo) {
                            $result = array_merge($result, $extractor->extract($generalInfo));
                        }
                        if (($schema = $parameter->getSchema()) instanceof Model\Schema) {
                            $result['schema'] = $extractor->extract($schema);
                        }

                        return $result;
                    }
                );
            },
            Model\Example::class => static function (ObjectExtractConfig $config) {
                $config->setCustomExtractor(
                    static function (Model\Example $example, ObjectExtractor $extractor) {
                        return $example->getEls();
                    }
                );
            },
            Model\SecurityRequirement::class => static function (ObjectExtractConfig $config) {
                $config->setCustomExtractor(
                    static function (Model\SecurityRequirement $object) {
                        return $object->getFields();
                    }
                );
            },
        ];
    }
}

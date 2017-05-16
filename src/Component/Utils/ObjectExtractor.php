<?php

namespace PhpSolution\SwaggerUIGen\Component\Utils;

/**
 * Class ObjectExtractor
 *
 * @package PhpSolution\SwaggerUIGen\Component\Utils
 */
class ObjectExtractor
{
    /**
     * @var \ArrayObject|ObjectExtractConfig[]
     */
    private $configList = [];
    /**
     * @var \Closure
     */
    private $processor;

    /**
     * ObjectExtractor constructor.
     *
     * @param array $configList
     */
    public function __construct(array $configList = [])
    {
        $this->configList = $configList;
    }

    /**
     * @param string              $class
     * @param ObjectExtractConfig $config
     */
    public function addExtractorConfig(string $class, ObjectExtractConfig $config): void
    {
        $this->configList[$class] = $config;
    }

    /**
     * @param $object
     *
     * @return array
     */
    public function extract($object): array
    {
        $class = get_class($object);
        $config = array_key_exists($class, $this->configList)
            ? $this->configList[$class]
            : $this->configList[$class] = new ObjectExtractConfig($class);

        if ((($customExtractor = $config->getCustomExtractor()) instanceof \Closure)) {
            $result = $customExtractor($object, $this);
        } else {
            if (is_null($this->processor)) {
                $this->processor = $this->getExtractProcessor();
            }
            $result = $this->processor->call($object, $this, $config);
        }

        return $result;
    }

    /**
     * @return \Closure
     */
    private function getExtractProcessor(): \Closure
    {
        return function (ObjectExtractor $objectExtractor, ObjectExtractConfig $config) {
            $result = [];
            $collectionsProps = $config->getCollectionsProps();
            $ignoredProps = $config->getIgnoredProps();
            $nameTransformer = $config->getNameTransformer();

            // Extract properties
            foreach ($config->getProps() as $propName) {
                if (!in_array($propName, $collectionsProps) && !in_array($propName, $ignoredProps)) {
                    $resultPropName = $nameTransformer[$propName] ?? $propName;
                    $propValue = $this->{$propName};
                    if (is_object($propValue)) {
                        $result[$resultPropName] = $propValue instanceof \JsonSerializable
                            ? $propValue->jsonSerialize()
                            : $objectExtractor->extract($propValue);
                    } elseif (!is_null($propValue)) {
                        $result[$resultPropName] = $propValue;
                    };
                }
            }

            // Extract collections
            foreach ($collectionsProps as $field) {
                $collectionValue = $this->{$field};
                $resultPropName = $nameTransformer[$field] ?? $field;
                if (is_array($collectionValue) || $collectionValue instanceof \Traversable) {
                    foreach ($collectionValue as $key => $value) {
                        $result[$resultPropName][$key] = $value instanceof \JsonSerializable
                            ? $value->jsonSerialize()
                            : $objectExtractor->extract($value);
                    }
                }
            }

            return $result;
        };
    }
}
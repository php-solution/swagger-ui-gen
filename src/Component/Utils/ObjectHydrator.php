<?php

namespace PhpSolution\SwaggerUIGen\Component\Utils;

/**
 * Class ObjectHydrator
 *
 * @package PhpSolution\SwaggerUIGen\Component\Utils
 */
class ObjectHydrator
{
    /**
     * @param object $object
     * @param array  $data
     * @param array  $fields
     * @param array  $transformers
     * @param array  $nameTransformers
     */
    public function hydrate($object, array $data, array $fields, array $transformers = [], array $nameTransformers = []): void
    {
        $hydrator = function (array $data, array $fields, array $transformers, array $nameTransformers) {
            foreach ($fields as $field) {
                if (array_key_exists($field, $data)) {
                    $propertyData = array_key_exists($field, $transformers)
                        ? $transformers[$field]($data[$field])
                        : $data[$field];
                    $propertyName = $nameTransformers[$field] ?? $field;
                    $this->{$propertyName} = $propertyData;
                }
            }
        };

        $hydrator->call($object, $data, $fields, $transformers, $nameTransformers);
    }

    /**
     * @param object $object
     * @param array  $data
     * @param array  $ignoredFields
     * @param array  $transformers
     * @param array  $nameTransformers
     */
    public function hydrateForIgnoredFields($object, array $data, array $ignoredFields, array $transformers = [], array $nameTransformers = []): void
    {
        $fieldsForHydrate = array_diff(array_keys($data), $ignoredFields);
        if (count($fieldsForHydrate) > 0) {
            $this->hydrate($object, $data, $fieldsForHydrate, $transformers, $nameTransformers);
        }
    }

    /**
     * @param object           $object
     * @param array            $data
     * @param array            $mapping
     * @param array|\Closure[] $transformers
     */
    public function hydrateWithMethods($object, array $data, array $mapping, array $transformers = []): void
    {
        foreach ($mapping as $dataField => $methodName) {
            if (array_key_exists($dataField, $data)) {
                $methodData = array_key_exists($dataField, $transformers)
                    ? $transformers[$dataField]($data[$dataField])
                    : $data[$dataField];
                $object->{$methodName}($methodData);
            }
        }
    }
}
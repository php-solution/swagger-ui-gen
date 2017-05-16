<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema;

use PhpSolution\SwaggerUIGen\Component\Model\Schema;

/**
 * Class ConfigRegistry
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema
 */
class ConfigRegistry extends \SplObjectStorage
{
    /**
     * @param Schema $schema
     * @param array  $config
     */
    public function mergeTo(Schema $schema, array $config): void
    {
        $existingConfig = $this->offsetExists($schema) ? $this->offsetGet($schema) : [];
        $config = array_merge_recursive($existingConfig, $config);
        $this->offsetSet($schema, $config);
    }

    /**
     * @param Schema   $schema
     * @param callable $reduceFunction
     */
    public function reduceConfig(Schema $schema, callable $reduceFunction): void
    {
        $currentConfig = $this->offsetGet($schema);
        $reduceConfig = $reduceFunction($currentConfig);
        $this->offsetSet($schema, $reduceConfig);
    }

    /**
     * @param Schema $schema
     * @param string $property
     * @param null   $default
     *
     * @return null
     */
    public function getMappingConfigValue(Schema $schema, string $property, $default = null)
    {
        switch (true) {
            case (isset($this[$schema]['mapping'][$property])):
                return $this[$schema]['mapping'][$property];
            case (($parent = $schema->getParent()) instanceof Schema):
                return isset($this[$parent]['mapping'][$property])
                    ? $this[$parent]['mapping'][$property]
                    : $this->getMappingConfigValue($parent, $property, $default);
            default:
                return $default;
        }
    }
}
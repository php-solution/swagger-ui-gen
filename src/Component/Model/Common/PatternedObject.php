<?php

namespace PhpSolution\SwaggerUIGen\Component\Model\Common;

/**
 * Class PatternedObject
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model\Common
 */
class PatternedObject
{
    /**
     * @var array|mixed[]
     */
    protected $extensionsFields = [];

    /**
     * @return array|\mixed[]
     */
    public function getExtensionsFields(): array
    {
        return $this->extensionsFields;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function addExtensionsField(string $name, $value): void
    {
        $this->extensionsFields[$name] = $value;
    }
}
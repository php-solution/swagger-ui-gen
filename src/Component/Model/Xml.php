<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

use PhpSolution\SwaggerUIGen\Component\Model\Common\NameOptionalTrait;
use PhpSolution\SwaggerUIGen\Component\Model\Common\PatternedObject;

/**
 * Class Xml
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class Xml extends PatternedObject
{
    use NameOptionalTrait;

    /**
     * @var string|null
     */
    private $namespace;
    /**
     * @var string|null
     */
    private $prefix;
    /**
     * @var bool
     */
    private $attribute = false;
    /**
     * @var bool
     */
    private $wrapped = false;

    /**
     * @return null|string
     */
    public function getNamespace():? string
    {
        return $this->namespace;
    }

    /**
     * @param null|string $namespace
     */
    public function setNamespace(?string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @return null|string
     */
    public function getPrefix():? string
    {
        return $this->prefix;
    }

    /**
     * @param null|string $prefix
     */
    public function setPrefix(?string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @return bool
     */
    public function isAttribute(): bool
    {
        return $this->attribute;
    }

    /**
     * @param bool $attribute
     */
    public function setAttribute(bool $attribute): void
    {
        $this->attribute = $attribute;
    }

    /**
     * @return bool
     */
    public function isWrapped(): bool
    {
        return $this->wrapped;
    }

    /**
     * @param bool $wrapped
     */
    public function setWrapped(bool $wrapped): void
    {
        $this->wrapped = $wrapped;
    }
}
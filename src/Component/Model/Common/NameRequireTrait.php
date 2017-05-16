<?php

namespace PhpSolution\SwaggerUIGen\Component\Model\Common;

/**
 * Class NameRequireTrait
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model\CommonTrait
 */
trait NameRequireTrait
{
    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
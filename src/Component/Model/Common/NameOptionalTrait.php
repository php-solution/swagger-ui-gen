<?php

namespace PhpSolution\SwaggerUIGen\Component\Model\Common;

/**
 * Class NameOptionalTrait
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model\CommonTrait
 */
trait NameOptionalTrait
{
    /**
     * @var string|null
     */
    private $name;

    /**
     * @return null|string
     */
    public function getName():? string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
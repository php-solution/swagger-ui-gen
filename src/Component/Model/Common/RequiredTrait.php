<?php

namespace PhpSolution\SwaggerUIGen\Component\Model\Common;

/**
 * Class RequiredTrait
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model\CommonTrait
 */
trait RequiredTrait
{
    /**
     * @var bool
     */
    protected $required = false;

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }
}
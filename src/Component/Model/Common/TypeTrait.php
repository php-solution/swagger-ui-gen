<?php

namespace PhpSolution\SwaggerUIGen\Component\Model\Common;

/**
 * Class TypeTrait
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model\Common
 */
trait TypeTrait
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
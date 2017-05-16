<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

/**
 * Class Example
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class Example
{
    /**
     * @var array
     */
    private $els = [];

    /**
     * @return array
     */
    public function getEls(): array
    {
        return $this->els;
    }

    /**
     * @param string $mimeType
     * @param mixed  $value
     */
    public function addEl(string $mimeType, $value): void
    {
        $this->els[$mimeType] = $value;
    }
}
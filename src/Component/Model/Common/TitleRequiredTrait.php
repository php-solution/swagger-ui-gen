<?php

namespace PhpSolution\SwaggerUIGen\Component\Model\Common;

/**
 * Class TitleRequiredTrait
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model\CommonTrait
 */
trait TitleRequiredTrait
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
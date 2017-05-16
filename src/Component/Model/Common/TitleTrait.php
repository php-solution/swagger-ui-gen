<?php

namespace PhpSolution\SwaggerUIGen\Component\Model\Common;

/**
 * Class TitleTrait
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model\CommonTrait
 */
trait TitleTrait
{
    /**
     * @var string|null
     */
    protected $title;

    /**
     * @return null|string
     */
    public function getTitle():? string
    {
        return $this->title;
    }

    /**
     * @param null|string $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }
}
<?php

namespace PhpSolution\SwaggerUIGen\Component\Model\Common;

/**
 * Class RequiredTrait
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model\CommonTrait
 */
trait DescriptionTrait
{
    /**
     * @var string|null
     */
    private $description;

    /**
     * @return null|string
     */
    public function getDescription():? string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param string $additionalDesc
     * @param string $delimiter
     */
    public function addDescription(string $additionalDesc, string $delimiter = '; '): void
    {
        $this->description .= (!empty($this->description) ? $delimiter : '') . $additionalDesc;
    }
}
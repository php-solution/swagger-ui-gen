<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

use PhpSolution\SwaggerUIGen\Component\Model\Common\DescriptionTrait;
use PhpSolution\SwaggerUIGen\Component\Model\Common\NameRequireTrait;

/**
 * Class Tag
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class Tag
{
    use NameRequireTrait, DescriptionTrait;

    /**
     * @var ExternalDocumentation|null
     */
    private $externalDocs;

    /**
     * @return null|ExternalDocumentation
     */
    public function getExternalDocs():? ExternalDocumentation
    {
        return $this->externalDocs;
    }

    /**
     * @param null|ExternalDocumentation $externalDocs
     */
    public function setExternalDocs(?ExternalDocumentation $externalDocs): void
    {
        $this->externalDocs = $externalDocs;
    }
}
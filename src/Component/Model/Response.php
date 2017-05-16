<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

use PhpSolution\SwaggerUIGen\Component\Model\Common\DescriptionTrait;

/**
 * Class Response
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class Response
{
    use DescriptionTrait;

    /**
     * @var Schema|null
     */
    private $schema;
    /**
     * @var array|Header[]|null
     */
    private $headers = [];
    /**
     * @var Example|null
     */
    private $examples;

    /**
     * @return null|Schema
     */
    public function getSchema():? Schema
    {
        return $this->schema;
    }

    /**
     * @param null|Schema $schema
     *
     * @return Response
     */
    public function setSchema(?Schema $schema): Response
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * @return null|array|Header[]
     */
    public function getHeaders():? array
    {
        return $this->headers;
    }

    /**
     * @param null|array|Header[] $headers
     */
    public function setHeaders(?array $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * @param string $fieldPattern
     * @param Header $header
     */
    public function addHeader(string $fieldPattern, Header $header): void
    {
        $this->headers[$fieldPattern] = $header;
    }

    /**
     * @return null|Example
     */
    public function getExamples():? Example
    {
        return $this->examples;
    }

    /**
     * @param null|Example $examples
     */
    public function setExamples(?Example $examples): void
    {
        $this->examples = $examples;
    }
}
<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

use PhpSolution\SwaggerUIGen\Component\Model\Common\DescriptionTrait;
use PhpSolution\SwaggerUIGen\Component\Model\Common\NameOptionalTrait;
use PhpSolution\SwaggerUIGen\Component\Model\Common\RequiredTrait;

/**
 * Class Parameter
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class Parameter
{
    public const IN_VALUES = ['query', 'header', 'path', 'formData', 'body'];
    public const IN_BODY = 'body';
    public const IN_QUERY = 'query';
    public const IN_PATH = 'path';
    public const IN_FORM_DATA = 'formData';
    public const IN_NODY = 'body';

    use NameOptionalTrait, DescriptionTrait, RequiredTrait;

    /**
     * @var string
     */
    private $in;
    /**
     * @var Schema|null
     */
    private $schema;
    /**
     * @var ParameterGeneralInfo|null
     */
    private $generalInfo;

    /**
     * Parameter constructor.
     *
     * @param string|null $in
     * @param string|null $name
     */
    public function __construct(string $in = null, string $name = null)
    {
        $this->in = $in;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getIn(): string
    {
        return $this->in;
    }

    /**
     * @param string $in
     */
    public function setIn(string $in): void
    {
        $this->in = $in;
    }

    /**
     * @return null|Schema
     */
    public function getSchema():? Schema
    {
        return $this->schema;
    }

    /**
     * @param null|Schema $schema
     */
    public function setSchema(?Schema $schema): void
    {
        $this->schema = $schema;
    }

    /**
     * @return null|ParameterGeneralInfo
     */
    public function getGeneralInfo():? ParameterGeneralInfo
    {
        return $this->generalInfo;
    }

    /**
     * @param null|ParameterGeneralInfo $generalInfo
     */
    public function setGeneralInfo(?ParameterGeneralInfo $generalInfo): void
    {
        $this->generalInfo = $generalInfo;
    }
}
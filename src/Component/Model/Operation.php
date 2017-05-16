<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

use PhpSolution\SwaggerUIGen\Component\Model\Common\DescriptionTrait;

/**
 * Class Operation
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class Operation
{
    use DescriptionTrait;

    /**
     * @var array|string[]
     */
    private $tags = [];
    /**
     * @var string|null
     */
    private $summary;
    /**
     * @var string|null
     */
    private $operationId;
    /**
     * @var array|string[]
     */
    private $consumes = [];
    /**
     * @var array|string[]
     */
    private $produces = [];
    /**
     * @var array|string[]
     */
    private $schemes = [];
    /**
     * @var bool
     */
    private $deprecated = false;
    /**
     * @var ExternalDocumentation|null
     */
    private $externalDocs;
    /**
     * @var array|Parameter[]
     */
    private $parameters = [];
    /**
     * @var array|SecurityRequirement[]
     */
    private $security = [];
    /**
     * @var array|Response[]
     */
    private $responses = [];

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

    /**
     * @return array|Response[]
     */
    public function getResponses(): array
    {
        return $this->responses;
    }

    /**
     * @param array|Response[] $responses
     */
    public function setResponses(array $responses): void
    {
        $this->responses = $responses;
    }

    /**
     * @param string   $statusCode
     * @param Response $response
     */
    public function addResponse(string $statusCode, Response $response): void
    {
        $this->responses[$statusCode] = $response;
    }

    /**
     * @return array|\string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array|\string[] $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return null|string
     */
    public function getSummary():? string
    {
        return $this->summary;
    }

    /**
     * @param null|string $summary
     */
    public function setSummary(?string $summary): void
    {
        $this->summary = $summary;
    }

    /**
     * @return null|string
     */
    public function getOperationId():? string
    {
        return $this->operationId;
    }

    /**
     * @param null|string $operationId
     */
    public function setOperationId(?string $operationId): void
    {
        $this->operationId = $operationId;
    }

    /**
     * @return array|\string[]
     */
    public function getConsumes(): array
    {
        return $this->consumes;
    }

    /**
     * @param array|\string[] $consumes
     */
    public function setConsumes(array $consumes): void
    {
        $this->consumes = $consumes;
    }

    /**
     * @return array|\string[]
     */
    public function getProduces(): array
    {
        return $this->produces;
    }

    /**
     * @param array|\string[] $produces
     */
    public function setProduces(array $produces): void
    {
        $this->produces = $produces;
    }

    /**
     * @return array|Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array|Parameter[] $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @param Parameter $parameter
     */
    public function addParameter(Parameter $parameter): void
    {
        $this->parameters[] = $parameter;
    }

    /**
     * @return array|\string[]
     */
    public function getSchemes(): array
    {
        return $this->schemes;
    }

    /**
     * @param array|\string[] $schemes
     */
    public function setSchemes(array $schemes): void
    {
        $this->schemes = $schemes;
    }

    /**
     * @return bool
     */
    public function isDeprecated(): bool
    {
        return $this->deprecated;
    }

    /**
     * @param bool $deprecated
     */
    public function setDeprecated(bool $deprecated): void
    {
        $this->deprecated = $deprecated;
    }

    /**
     * @return array|SecurityRequirement[]
     */
    public function getSecurity()
    {
        return $this->security;
    }

    /**
     * @param array|SecurityRequirement[] $security
     */
    public function setSecurity(array $security): void
    {
        $this->security = $security;
    }

    /**
     * @param SecurityRequirement $security
     */
    public function addSecurity(SecurityRequirement $security): void
    {
        $this->security[] = $security;
    }
}
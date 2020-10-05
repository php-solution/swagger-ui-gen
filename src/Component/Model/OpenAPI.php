<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

/**
 * Class Config
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 * @see     https://github.com/OAI/OpenAPI-Specification/blob/master/versions/2.0.md#swagger-object
 */
class OpenAPI
{
    public const DEFAULT_VERSION = '3.0.0';

    public const SCHEMES = ['http', 'https', 'ws', 'wss'];

    public const SCHEMES_DEFAULT = ['http'];

    /**
     * @var string
     */
    private $openapi;

    /**
     * @var array|string[]
     */
    private $servers = [];

    /**
     * @var \PhpSolution\SwaggerUIGen\Component\Model\Components
     */
    private $components;

    /**
     * @var Info
     */
    private $info;

    /**
     * @var array|PathItem[]
     */
    private $paths = [];

    /**
     * @var array|Parameter[]|null
     */
    private $parameters = [];

    /**
     * @var array|Response[]|null
     */
    private $responses;

    /**
     * @var array|SecurityRequirement[]
     */
    private $security = [];

    /**
     * @var array|Tag[]
     */
    private $tags = [];

    /**
     * @var ExternalDocumentation|null
     */
    private $externalDocs;

    /**
     * @return string
     */
    public function getOpenapi(): string
    {
        return $this->openapi;
    }

    /**
     * @param string $openapi
     */
    public function setOpenapi(string $openapi): void
    {
        $this->openapi = $openapi;
    }

    public function getServers(): array
    {
        return $this->servers;
    }

    public function setServers($servers): void
    {
        $this->servers = $servers;
    }

    public function getComponents(): Components
    {
        return $this->components;
    }

    public function setComponents(Components $components): void
    {
        $this->components = $components;
    }

    /**
     * @param string $name
     * @param Schema $schema
     */
    public function addSchemaToComponent(string $name, Schema $schema): void
    {
        if (null === $this->components) {
            $this->components = new Components();
        }

        $this->components->addSchema($name, $schema);
    }

    /**
     * @return Info
     */
    public function getInfo(): Info
    {
        return $this->info;
    }

    /**
     * @param Info $info
     */
    public function setInfo(Info $info): void
    {
        $this->info = $info;
    }

    /**
     * @return array|null|PathItem[]
     */
    public function getPaths(): ?array
    {
        return $this->paths;
    }

    /**
     * @param array|null|PathItem[] $paths
     */
    public function setPaths(?array $paths): void
    {
        $this->paths = $paths;
    }

    /**
     * @param string   $pathName
     * @param PathItem $pathItem
     */
    public function addPathItem(string $pathName, PathItem $pathItem): void
    {
        if ($this->paths === null) {
            $this->paths = [];
        }

        $this->paths[$pathName] = $pathItem;
    }

    /**
     * @param string $pathName
     *
     * @return bool
     */
    public function hasPathItem(string $pathName): bool
    {
        return \array_key_exists($pathName, $this->paths);
    }

    /**
     * @param string $pathName
     *
     * @return PathItem
     */
    public function getPathItem(string $pathName): PathItem
    {
        return $this->paths[$pathName];
    }

    /**
     * @return null|array|Parameter[]
     */
    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    /**
     * @param null|array|Parameter[] $parameters
     */
    public function setParameters(?array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return null|array|Response[]
     */
    public function getResponses(): ?array
    {
        return $this->responses;
    }

    /**
     * @param null|array|Response[] $responses
     */
    public function setResponses(?array $responses): void
    {
        $this->responses = $responses;
    }

    /**
     * @param string   $name
     * @param Response $response
     */
    public function addResponse(string $name, Response $response): void
    {
        $this->responses[$name] = $response;
    }

    /**
     * @return array|SecurityRequirement[]
     */
    public function getSecurity(): array
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
     * @param SecurityRequirement $securityRequirement
     */
    public function addSecurity(SecurityRequirement $securityRequirement): void
    {
        $this->security[] = $securityRequirement;
    }

    /**
     * @return array|Tag[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array|Tag[] $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @param Tag $tag
     */
    public function addTag(Tag $tag): void
    {
        $this->tags[] = $tag;
    }

    /**
     * @return null|ExternalDocumentation
     */
    public function getExternalDocs(): ?ExternalDocumentation
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
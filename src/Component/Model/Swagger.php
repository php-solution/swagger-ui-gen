<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

/**
 * Class Config
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 * @see     https://github.com/OAI/OpenAPI-Specification/blob/master/versions/2.0.md#swagger-object
 */
class Swagger
{
    public const DEFAULT_VERSION = '2.0';
    public const SCHEMES = ['http', 'https', 'ws', 'wss'];
    public const SCHEMES_DEFAULT = ['http'];

    /**
     * @var string
     */
    private $swagger;
    /**
     * @var string|null
     */
    private $host;
    /**
     * @var string|null
     */
    private $basePath;
    /**
     * @var array|string[]
     */
    private $schemes = [];
    /**
     * @var array|string[]
     */
    private $consumes = [];
    /**
     * @var array|string[]
     */
    private $produces = [];
    /**
     * @var Info
     */
    private $info;
    /**
     * @var array|PathItem[]
     */
    private $paths = [];
    /**
     * @var array|Schema[]|null
     */
    private $definitions = [];
    /**
     * @var array|Parameter[]|null
     */
    private $parameters = [];
    /**
     * @var array|Response[]|null
     */
    private $responses;
    /**
     * @var array|SecurityScheme[]|null
     */
    private $securityDefinitions;
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
    public function getSwagger(): string
    {
        return $this->swagger;
    }

    /**
     * @param string $swagger
     */
    public function setSwagger(string $swagger): void
    {
        $this->swagger = $swagger;
    }

    /**
     * @return null|string
     */
    public function getHost():? string
    {
        return $this->host;
    }

    /**
     * @param null|string $host
     */
    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return null|string
     */
    public function getBasePath():? string
    {
        return $this->basePath;
    }

    /**
     * @param null|string $basePath
     */
    public function setBasePath(?string $basePath): void
    {
        $this->basePath = $basePath;
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
    public function setProduces($produces): void
    {
        $this->produces = $produces;
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
    public function getPaths():? array
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
        if (is_null($this->paths)) {
            $this->paths = [];
        }
        $this->paths[$pathName] = $pathItem;
    }

    /**
     * @return null|array|Schema[]
     */
    public function getDefinitions():? array
    {
        return $this->definitions;
    }

    /**
     * @param null|array|Schema[] $definitions
     */
    public function setDefinitions(?array $definitions): void
    {
        $this->definitions = $definitions;
    }

    /**
     * @param string $name
     * @param Schema $definition
     */
    public function addDefinition(string $name, Schema $definition): void
    {
        $this->definitions[$name] = $definition;
    }

    /**
     * @return null|array|Parameter[]
     */
    public function getParameters():? array
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
    public function getResponses():? array
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
     * @return null|array|SecurityScheme[]
     */
    public function getSecurityDefinitions():? array
    {
        return $this->securityDefinitions;
    }

    /**
     * @param null|array|SecurityScheme[] $securityDefinitions
     */
    public function setSecurityDefinitions(?array $securityDefinitions): void
    {
        $this->securityDefinitions = $securityDefinitions;
    }

    /**
     * @param string         $name
     * @param SecurityScheme $securityScheme
     */
    public function addSecurityDefinitions(string $name, SecurityScheme $securityScheme): void
    {
        if (is_null($this->securityDefinitions)) {
            $this->securityDefinitions = [];
        }
        $this->securityDefinitions[$name] = $securityScheme;
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
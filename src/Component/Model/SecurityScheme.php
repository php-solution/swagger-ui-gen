<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

use PhpSolution\SwaggerUIGen\Component\Model\Common\BasicObject;
use PhpSolution\SwaggerUIGen\Component\Model\Common\CollectionAliasTrait;
use PhpSolution\SwaggerUIGen\Component\Model\Common\DescriptionTrait;
use PhpSolution\SwaggerUIGen\Component\Model\Common\NameRequireTrait;

/**
 * Class SecurityScheme
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class SecurityScheme extends BasicObject
{
    public const TYPES = ['basic', 'apiKey', 'oauth2'];
    public const INS = ['query', 'header'];
    public const FLOWS = ["implicit", "password", "application", "accessCode"];

    use DescriptionTrait, NameRequireTrait, CollectionAliasTrait;

    /**
     * @var string
     */
    private $in;
    /**
     * @var string
     */
    private $flow;
    /**
     * @var string
     */
    private $authorizationUrl;
    /**
     * @var string
     */
    private $tokenUrl;
    /**
     * @var array
     */
    private $scopes = [];

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
     * @return string
     */
    public function getFlow(): string
    {
        return $this->flow;
    }

    /**
     * @param string $flow
     */
    public function setFlow(string $flow): void
    {
        $this->flow = $flow;
    }

    /**
     * @return string
     */
    public function getAuthorizationUrl(): string
    {
        return $this->authorizationUrl;
    }

    /**
     * @param string $authorizationUrl
     */
    public function setAuthorizationUrl(string $authorizationUrl): void
    {
        $this->authorizationUrl = $authorizationUrl;
    }

    /**
     * @return string
     */
    public function getTokenUrl(): string
    {
        return $this->tokenUrl;
    }

    /**
     * @param string $tokenUrl
     */
    public function setTokenUrl(string $tokenUrl): void
    {
        $this->tokenUrl = $tokenUrl;
    }

    /**
     * @return array
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * @param array $scopes
     */
    public function setScopes(array $scopes): void
    {
        $this->scopes = $scopes;
    }
}
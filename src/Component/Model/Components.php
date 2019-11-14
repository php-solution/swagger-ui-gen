<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

class Components
{
    /**
     * @var \PhpSolution\SwaggerUIGen\Component\Model\SecurityScheme[]|array
     */
    private $securitySchemes = [];

    /**
     * @var \PhpSolution\SwaggerUIGen\Component\Model\Schema[]|array
     */
    private $schemas = [];

    /**
     * @return \PhpSolution\SwaggerUIGen\Component\Model\SecurityScheme[]|array
     */
    public function getSecuritySchemes(): array
    {
        return $this->securitySchemes;
    }

    public function setSecuritySchemes($securitySchemes): void
    {
        $this->securitySchemes = $securitySchemes;
    }

    /**
     * @return \PhpSolution\SwaggerUIGen\Component\Model\Schema[]|array
     */
    public function getSchemas(): array
    {
        return $this->schemas;
    }

    public function setSchemas($schemas): void
    {
        $this->schemas = $schemas;
    }

    public function addSchema(string $name, Schema $schema): void
    {
        $this->schemas[$name] = $schema;
    }
}
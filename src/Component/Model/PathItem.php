<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

use PhpSolution\SwaggerUIGen\Component\Model\Common\CollectionAliasTrait;

/**
 * Class PathItem
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class PathItem
{
    public const OPERATION_NAMES = ['get', 'put', 'post', 'delete', 'options', 'head', 'patch'];
    public const GET = 'get';
    public const PUT = 'put';
    public const POST = 'post';
    public const DELETE = 'delete';
    public const OPTIONS = 'options';
    public const HEAD = 'head';
    public const PATCH = 'patch';

    use CollectionAliasTrait;

    /**
     * @var string|null
     */
    private $ref;
    /**
     * @var array|Parameter[]
     */
    private $parameters = [];
    /**
     * @var array|Operation[]
     */
    private $operations = [];

    /**
     * @return null|string
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @param null|string $ref
     */
    public function setRef(?string $ref): void
    {
        $this->ref = $ref;
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
     * @return array|Operation[]
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    /**
     * @param array|Operation[] $operations
     */
    public function setOperations(array $operations): void
    {
        $this->operations = $operations;
    }

    /**
     * @param string $name
     *
     * @return null|Operation
     */
    public function getOperation(string $name):? Operation
    {
        return $this->operations[$name] ?? null;
    }


    /**
     * @param string         $name
     * @param null|Operation $operation
     */
    public function addOperation(string $name, ?Operation $operation): void
    {
        $this->operations[$name] = $operation;
    }
}
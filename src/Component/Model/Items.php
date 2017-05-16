<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

use PhpSolution\SwaggerUIGen\Component\Model\Common\BasicObject;

/**
 * Class Items
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class Items extends BasicObject
{
    public const TYPES = ['string', 'number', 'integer', 'boolean', 'array'];
    public const TYPES_TRANSFORMER = [
        'boolean' => 'boolean',
        'integer' => 'integer',
        'double' => 'number',
        'string' => 'string',
        'array' => 'array',
        'object' => 'array',
        'resource' => 'array',
        'NULL' => 'string',
        'unknown type' => 'string'
    ];
    /**
     * @var null|Items
     */
    protected $items;
    /**
     * @var string|null
     */
    protected $collectionFormat;

    /**
     * Items constructor.
     *
     * @param string|null $type
     */
    public function __construct(string $type = null)
    {
        $this->type = $type;
    }

    /**
     * @return null|Items
     */
    public function getItems():? Items
    {
        return $this->items;
    }

    /**
     * @param null|Items $items
     */
    public function setItems(?Items $items): void
    {
        $this->items = $items;
    }

    /**
     * @return null|string
     */
    public function getCollectionFormat():? string
    {
        return $this->collectionFormat;
    }

    /**
     * @param null|string $collectionFormat
     */
    public function setCollectionFormat(?string $collectionFormat): void
    {
        $this->collectionFormat = $collectionFormat;
    }

    /**
     * @param mixed $variable
     */
    public function setTypeByVariable($variable): void
    {
        $phpType = gettype($variable);
        $this->setType(static::TYPES_TRANSFORMER[$phpType]);
    }
}
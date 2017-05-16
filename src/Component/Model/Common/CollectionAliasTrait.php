<?php

namespace PhpSolution\SwaggerUIGen\Component\Model\Common;

/**
 * Class ItemAliasTrait
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model\Common
 */
trait CollectionAliasTrait
{
    /**
     * @var string
     */
    protected $collectionItemAlias;

    /**
     * @return string
     */
    public function getCollectionItemAlias(): string
    {
        return $this->collectionItemAlias;
    }

    /**
     * @param string $collectionItemAlias
     */
    public function setCollectionItemAlias(string $collectionItemAlias)
    {
        $this->collectionItemAlias = $collectionItemAlias;
    }
}
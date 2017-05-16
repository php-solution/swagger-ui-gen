<?php

namespace Project\AdminBundle\Lib;

/**
 * Class PaginatedCollection
 *
 * @package Project\AdminBundle\Lib
 */
class PaginatedCollection
{
    const DEFAULT_LIMIT = 10;
    /**
     * @var int
     */
    private $totalCount = 0;
    /**
     * @var int
     */
    private $offset = 0;
    /**
     * @var int
     */
    private $limit = self::DEFAULT_LIMIT;
    /**
     * @var array
     */
    private $items = [];

    /**
     * PaginatedCollection constructor.
     *
     * @param int   $totalCount
     * @param array $items
     * @param int   $offset
     * @param int   $limit
     */
    public function __construct(int $totalCount = 0, array $items, int $offset = 0, int $limit = self::DEFAULT_LIMIT)
    {
        $this->totalCount = $totalCount;
        $this->items = $items;
        $this->offset = $offset;
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     *
     * @return PaginatedCollection
     */
    public function setTotalCount(int $totalCount): PaginatedCollection
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     *
     * @return PaginatedCollection
     */
    public function setOffset(int $offset): PaginatedCollection
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     *
     * @return PaginatedCollection
     */
    public function setLimit(int $limit): PaginatedCollection
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     *
     * @return PaginatedCollection
     */
    public function setItems(array $items): PaginatedCollection
    {
        $this->items = $items;

        return $this;
    }
}
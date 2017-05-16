<?php

namespace PhpSolution\SwaggerUIGen\Component\Utils;

/**
 * Class ObjectExtractConfig
 *
 * @package PhpSolution\SwaggerUIGen\Component\Utils
 */
class ObjectExtractConfig
{
    /**
     * @var int
     */
    private $refPropsFilter = \ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED;
    /**
     * @var bool
     */
    private $useRefProps = true;
    /**
     * @var string
     */
    private $class;
    /**
     * @var array
     */
    private $props;
    /**
     * @var array
     */
    private $collectionsProps = [];
    /**
     * @var array
     */
    private $ignoredProps = [];
    /**
     * @var array
     */
    private $nameTransformer = [];
    /**
     * @var null|\Closure
     */
    private $customExtractor;

    /**
     * ObjectExtractorConfig constructor.
     *
     * @param string|null $class
     */
    public function __construct(string $class = null)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return ObjectExtractConfig
     */
    public function setClass(string $class): ObjectExtractConfig
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return array
     */
    public function getProps(): array
    {
        if ($this->useRefProps && is_null($this->props)) {
            $classRef = new \ReflectionClass($this->class);
            $refPropertyList = $classRef->getProperties($this->refPropsFilter);
            $this->props = array_map(
                function (\ReflectionProperty $property) {
                    return $property->getName();
                }, $refPropertyList
            );
        } elseif (is_null($this->props)) {
            $this->props = [];
        }

        return $this->props;
    }

    /**
     * @param array $props
     *
     * @return ObjectExtractConfig
     */
    public function setProps(array $props): ObjectExtractConfig
    {
        $this->props = $props;

        return $this;
    }

    /**
     * @return int
     */
    public function getRefPropsFilter(): int
    {
        return $this->refPropsFilter;
    }

    /**
     * @param int $refPropsFilter
     *
     * @return ObjectExtractConfig
     */
    public function setRefPropsFilter(int $refPropsFilter): ObjectExtractConfig
    {
        $this->refPropsFilter = $refPropsFilter;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUseRefProps(): bool
    {
        return $this->useRefProps;
    }

    /**
     * @param bool $useRefProps
     *
     * @return ObjectExtractConfig
     */
    public function setUseRefProps(bool $useRefProps): ObjectExtractConfig
    {
        $this->useRefProps = $useRefProps;

        return $this;
    }

    /**
     * @return array
     */
    public function getCollectionsProps(): array
    {
        return $this->collectionsProps;
    }

    /**
     * @param array $collectionsProps
     *
     * @return ObjectExtractConfig
     */
    public function setCollectionsProps(array $collectionsProps): ObjectExtractConfig
    {
        $this->collectionsProps = $collectionsProps;

        return $this;
    }

    /**
     * @return array
     */
    public function getIgnoredProps(): array
    {
        return $this->ignoredProps;
    }

    /**
     * @param array $ignoredProps
     *
     * @return ObjectExtractConfig
     */
    public function setIgnoredProps(array $ignoredProps): ObjectExtractConfig
    {
        $this->ignoredProps = $ignoredProps;

        return $this;
    }

    /**
     * @return array
     */
    public function getNameTransformer(): array
    {
        return $this->nameTransformer;
    }

    /**
     * @param array $nameTransformer
     *
     * @return ObjectExtractConfig
     */
    public function setNameTransformer(array $nameTransformer): ObjectExtractConfig
    {
        $this->nameTransformer = $nameTransformer;

        return $this;
    }

    /**
     * @return \Closure|null
     */
    public function getCustomExtractor():? \Closure
    {
        return $this->customExtractor;
    }

    /**
     * @param \Closure|null $customExtractor
     *
     * @return ObjectExtractConfig
     */
    public function setCustomExtractor($customExtractor): ObjectExtractConfig
    {
        $this->customExtractor = $customExtractor;

        return $this;
    }
}
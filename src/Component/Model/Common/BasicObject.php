<?php

namespace PhpSolution\SwaggerUIGen\Component\Model\Common;

/**
 * Class BasicPropertiesTrait
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class BasicObject extends PatternedObject
{
    /**
     * @var string|null
     */
    protected $type;
    /**
     * @var string|null
     */
    protected $format;
    /**
     * @var bool
     */
    protected $allowEmptyValue = false;
    /**
     * @var string|null
     */
    protected $default;
    /**
     * @var int|null
     */
    protected $maximum;
    /**
     * @var bool|null
     */
    protected $exclusiveMaximum;
    /**
     * @var int|null
     */
    protected $minimum;
    /**
     * @var bool|null
     */
    protected $exclusiveMinimum;
    /**
     * @var int|null
     */
    protected $maxLength;
    /**
     * @var int|null
     */
    protected $minLength;
    /**
     * @var string|null
     */
    protected $pattern;
    /**
     * @var int|null
     */
    protected $maxItems;
    /**
     * @var int|null
     */
    protected $minItems;
    /**
     * @var bool|null
     */
    protected $uniqueItems;
    /**
     * @var array|null
     */
    protected $enum;
    /**
     * @var int|null
     */
    protected $multipleOf;

    /**
     * @return null|string
     */
    public function getType():? string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return null|string
     */
    public function getFormat():? string
    {
        return $this->format;
    }

    /**
     * @param null|string $format
     */
    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }

    /**
     * @return bool
     */
    public function isAllowEmptyValue(): bool
    {
        return $this->allowEmptyValue;
    }

    /**
     * @param bool $allowEmptyValue
     */
    public function setAllowEmptyValue(bool $allowEmptyValue): void
    {
        $this->allowEmptyValue = $allowEmptyValue;
    }

    /**
     * @return string
     */
    public function getDefault(): string
    {
        return (string) $this->default;
    }

    /**
     * @param null|string $default
     */
    public function setDefault($default): void
    {
        $this->default = $default;
    }

    /**
     * @return int|null
     */
    public function getMaximum():? int
    {
        return $this->maximum;
    }

    /**
     * @param int|null $maximum
     */
    public function setMaximum($maximum): void
    {
        $this->maximum = $maximum;
    }

    /**
     * @return bool|null
     */
    public function getExclusiveMaximum():? bool
    {
        return $this->exclusiveMaximum;
    }

    /**
     * @param bool|null $exclusiveMaximum
     */
    public function setExclusiveMaximum($exclusiveMaximum): void
    {
        $this->exclusiveMaximum = $exclusiveMaximum;
    }

    /**
     * @return int|null
     */
    public function getMinimum():? int
    {
        return $this->minimum;
    }

    /**
     * @param int|null $minimum
     */
    public function setMinimum($minimum): void
    {
        $this->minimum = $minimum;
    }

    /**
     * @return bool|null
     */
    public function getExclusiveMinimum():? bool
    {
        return $this->exclusiveMinimum;
    }

    /**
     * @param bool|null $exclusiveMinimum
     */
    public function setExclusiveMinimum($exclusiveMinimum): void
    {
        $this->exclusiveMinimum = $exclusiveMinimum;
    }

    /**
     * @return int|null
     */
    public function getMaxLength():? int
    {
        return $this->maxLength;
    }

    /**
     * @param int|null $maxLength
     */
    public function setMaxLength($maxLength): void
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @return int|null
     */
    public function getMinLength():? int
    {
        return $this->minLength;
    }

    /**
     * @param int|null $minLength
     */
    public function setMinLength($minLength): void
    {
        $this->minLength = $minLength;
    }

    /**
     * @return null|string
     */
    public function getPattern():? string
    {
        return $this->pattern;
    }

    /**
     * @param null|string $pattern
     */
    public function setPattern(?string $pattern): void
    {
        $this->pattern = $pattern;
    }

    /**
     * @return int|null
     */
    public function getMaxItems():? int
    {
        return $this->maxItems;
    }

    /**
     * @param int|null $maxItems
     */
    public function setMaxItems(?int $maxItems): void
    {
        $this->maxItems = $maxItems;
    }

    /**
     * @return int|null
     */
    public function getMinItems():? int
    {
        return $this->minItems;
    }

    /**
     * @param int|null $minItems
     */
    public function setMinItems($minItems): void
    {
        $this->minItems = $minItems;
    }

    /**
     * @return bool|null
     */
    public function getUniqueItems():? bool
    {
        return $this->uniqueItems;
    }

    /**
     * @param bool|null $uniqueItems
     */
    public function setUniqueItems($uniqueItems): void
    {
        $this->uniqueItems = $uniqueItems;
    }

    /**
     * @return array|null
     */
    public function getEnum():? array
    {
        return $this->enum;
    }

    /**
     * @param array|null $enum
     */
    public function setEnum($enum): void
    {
        $this->enum = $enum;
    }

    /**
     * @return int|null
     */
    public function getMultipleOf():? int
    {
        return $this->multipleOf;
    }

    /**
     * @param int|null $multipleOf
     */
    public function setMultipleOf($multipleOf): void
    {
        $this->multipleOf = $multipleOf;
    }
}
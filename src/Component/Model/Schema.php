<?php

namespace PhpSolution\SwaggerUIGen\Component\Model;

/**
 * Class Schema
 *
 * @package PhpSolution\SwaggerUIGen\Component\Model
 */
class Schema
{
    /**
     * @var string
     */
    private $ref;
    /**
     * @var string|null
     */
    private $description;
    /**
     * @var string
     */
    private $format;
    /**
     * @var string
     */
    private $title;
    /**
     * @var mixed
     */
    private $default;
    /**
     * @var int
     */
    private $multipleOf;
    /**
     * @var int|null
     */
    private $maximum;
    /**
     * @var bool
     */
    private $exclusiveMaximum;
    /**
     * @var int|null
     */
    private $minimum;
    /**
     * @var bool
     */
    private $exclusiveMinimum;
    /**
     * @var int|null
     */
    private $maxLength;
    /**
     * @var int|null
     */
    private $minLength;
    /**
     * @var string|null
     */
    private $pattern;
    /**
     * @var int|null
     */
    private $maxItems;
    /**
     * @var int|null
     */
    private $minItems;
    /**
     * @var bool
     */
    private $uniqueItems;
    /**
     * @var int|null
     */
    private $maxProperties;
    /**
     * @var int|null
     */
    private $minProperties;
    /**
     * @var array|string[]
     */
    private $required;
    /**
     * @var array
     */
    private $enum;
    /**
     * @var string|null
     */
    private $discriminator;
    /**
     * @var bool
     */
    private $readOnly;
    /**
     * @var array|null
     */
    private $xml;
    /**
     * @var ExternalDocumentation|null
     */
    private $externalDocs;
    /**
     * @var mixed
     */
    private $example;
    /**
     * @var string
     */
    private $type;
    /**
     * @var array|Schema[]
     */
    private $additionalProperties = [];
    /**
     * @var Schema|null
     */
    private $items;
    /**
     * @var array|Schema[]
     */
    private $allOf = [];
    /**
     * @var array|Schema[]
     */
    private $properties = [];
    /**
     * @var Schema|null
     */
    private $parent;

    /**
     * Schema constructor.
     *
     * @param string|null $type
     */
    public function __construct(string $type = 'string')
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getRef(): string
    {
        return $this->ref;
    }

    /**
     * @param string $ref
     */
    public function setRef(?string $ref)
    {
        $this->ref = $ref;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     */
    public function setFormat(?string $format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(?string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return int
     */
    public function getMultipleOf(): int
    {
        return $this->multipleOf;
    }

    /**
     * @param int $multipleOf
     */
    public function setMultipleOf(int $multipleOf)
    {
        $this->multipleOf = $multipleOf;
    }

    /**
     * @return int|null
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * @param int|null $maximum
     */
    public function setMaximum($maximum)
    {
        $this->maximum = $maximum;
    }

    /**
     * @return bool
     */
    public function isExclusiveMaximum(): bool
    {
        return $this->exclusiveMaximum;
    }

    /**
     * @param bool $exclusiveMaximum
     */
    public function setExclusiveMaximum(bool $exclusiveMaximum)
    {
        $this->exclusiveMaximum = $exclusiveMaximum;
    }

    /**
     * @return int|null
     */
    public function getMinimum()
    {
        return $this->minimum;
    }

    /**
     * @param int|null $minimum
     */
    public function setMinimum($minimum)
    {
        $this->minimum = $minimum;
    }

    /**
     * @return bool
     */
    public function isExclusiveMinimum(): bool
    {
        return $this->exclusiveMinimum;
    }

    /**
     * @param bool $exclusiveMinimum
     */
    public function setExclusiveMinimum(bool $exclusiveMinimum)
    {
        $this->exclusiveMinimum = $exclusiveMinimum;
    }

    /**
     * @return int|null
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @param int|null $maxLength
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
    }

    /**
     * @return int|null
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * @param int|null $minLength
     */
    public function setMinLength($minLength)
    {
        $this->minLength = $minLength;
    }

    /**
     * @return null|string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param null|string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return int|null
     */
    public function getMaxItems()
    {
        return $this->maxItems;
    }

    /**
     * @param int|null $maxItems
     */
    public function setMaxItems($maxItems)
    {
        $this->maxItems = $maxItems;
    }

    /**
     * @return int|null
     */
    public function getMinItems()
    {
        return $this->minItems;
    }

    /**
     * @param int|null $minItems
     */
    public function setMinItems($minItems)
    {
        $this->minItems = $minItems;
    }

    /**
     * @return bool
     */
    public function isUniqueItems(): bool
    {
        return $this->uniqueItems;
    }

    /**
     * @param bool $uniqueItems
     */
    public function setUniqueItems(bool $uniqueItems)
    {
        $this->uniqueItems = $uniqueItems;
    }

    /**
     * @return int|null
     */
    public function getMaxProperties()
    {
        return $this->maxProperties;
    }

    /**
     * @param int|null $maxProperties
     */
    public function setMaxProperties($maxProperties)
    {
        $this->maxProperties = $maxProperties;
    }

    /**
     * @return int|null
     */
    public function getMinProperties()
    {
        return $this->minProperties;
    }

    /**
     * @param int|null $minProperties
     */
    public function setMinProperties($minProperties)
    {
        $this->minProperties = $minProperties;
    }

    /**
     * @return array|\string[]
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param array|\string[] $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @param string $name
     */
    public function addRequired(string $name): void
    {
        if (!is_array($this->required)) {
            $this->required = [];
        }
        $this->required[] = $name;
    }

    /**
     * @return null|string
     */
    public function getNameOnParentProperties():? string
    {
        if (($parent = $this->getParent()) instanceof Schema) {
            foreach ($parent->getProperties() as $name => $parentProperty) {
                if ($parentProperty === $this) {
                    return $name;
                }
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getEnum(): array
    {
        return $this->enum;
    }

    /**
     * @param array $enum
     */
    public function setEnum(array $enum)
    {
        $this->enum = $enum;
    }

    /**
     * @return null|string
     */
    public function getDiscriminator()
    {
        return $this->discriminator;
    }

    /**
     * @param null|string $discriminator
     */
    public function setDiscriminator($discriminator)
    {
        $this->discriminator = $discriminator;
    }

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * @param bool $readOnly
     */
    public function setReadOnly(bool $readOnly)
    {
        $this->readOnly = $readOnly;
    }

    /**
     * @return array|null
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * @param array|null $xml
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
    }

    /**
     * @return null|ExternalDocumentation
     */
    public function getExternalDocs()
    {
        return $this->externalDocs;
    }

    /**
     * @param null|ExternalDocumentation $externalDocs
     */
    public function setExternalDocs($externalDocs)
    {
        $this->externalDocs = $externalDocs;
    }

    /**
     * @return mixed
     */
    public function getExample()
    {
        return $this->example;
    }

    /**
     * @param mixed $example
     */
    public function setExample($example)
    {
        $this->example = $example;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return array|Schema[]
     */
    public function getAdditionalProperties()
    {
        return $this->additionalProperties;
    }

    /**
     * @param array|Schema[] $additionalProperties
     */
    public function setAdditionalProperties($additionalProperties)
    {
        $this->additionalProperties = $additionalProperties;
    }

    /**
     * @return null|Schema
     */
    public function getItems():? Schema
    {
        return $this->items;
    }

    /**
     * @param null|Schema $items
     */
    public function setItems(?Schema $items): void
    {
        $this->items = $items;
    }

    /**
     * @return array|Schema[]
     */
    public function getAllOf()
    {
        return $this->allOf;
    }

    /**
     * @param array|Schema[] $allOf
     */
    public function setAllOf($allOf)
    {
        $this->allOf = $allOf;
    }

    /**
     * @return array|Schema[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array|Schema[] $properties
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @param string $name
     * @param Schema $schema
     */
    public function addAdditionalProperty(string $name, Schema $schema): void
    {
        $this->additionalProperties[$name] = $schema;
    }

    /**
     * @param string $name
     * @param Schema $schema
     */
    public function addAllOf(string $name, Schema $schema): void
    {
        $this->allOf[$name] = $schema;
    }

    /**
     * @param string $name
     * @param Schema $schema
     */
    public function addProperty(string $name, Schema $schema): void
    {
        $schema->setParent($this);
        $this->properties[$name] = $schema;
    }

    /**
     * @param string $name
     *
     * @return null|Schema
     */
    public function getProperty(string $name):? Schema
    {
        return array_key_exists($name, $this->properties) ? $this->properties[$name] : null;
    }

    /**
     * @param string $name
     */
    public function removeProperty(string $name): void
    {
        if (array_key_exists($name, $this->properties)) {
            $this->properties[$name]->setParent(null);
            unset($this->properties[$name]);
        }
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasProperty(string $name): bool
    {
        return array_key_exists($name, $this->properties);
    }

    /**
     * @param null|Schema $schema
     */
    public function setParent(?Schema $schema): void
    {
        $this->parent = $schema;
    }

    /**
     * @return null|Schema
     */
    public function getParent():? Schema
    {
        return $this->parent;
    }
}
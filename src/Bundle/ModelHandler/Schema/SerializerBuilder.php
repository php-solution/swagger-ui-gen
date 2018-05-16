<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema;

use PhpSolution\SwaggerUIGen\Component\Model\Schema;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

/**
 * Class SerializerBuilder
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema
 */
class SerializerBuilder implements SchemaBuilderInterface
{
    /**
     * @var ClassMetadataFactoryInterface
     */
    private $serializerMetaData;

    /**
     * SerializerBuilder constructor.
     *
     * @param ClassMetadataFactoryInterface $serializerMetaData
     */
    public function __construct(ClassMetadataFactoryInterface $serializerMetaData)
    {
        $this->serializerMetaData = $serializerMetaData;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 10;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'serializer';
    }

    /**
     * Serializer Builder add property from serialization metadata,
     * remove if config includes group and serialize does not has all of config groups
     *
     * @param Schema         $schema
     * @param ConfigRegistry $configRegistry
     */
    public function buildSchema(Schema $schema, ConfigRegistry $configRegistry): void
    {
        $config = $configRegistry[$schema] ?? [];
        if (!isset($config['mapping']['class'])) {
            return;
        }

        $configGroups = (array) $configRegistry->getMappingConfigValue($schema, 'serializer_groups');
        $serializeMapping = $this->serializerMetaData->getMetadataFor($config['mapping']['class']);

        foreach ($serializeMapping->getAttributesMetadata() as $metadata) {
            $propertyName = $metadata->getName();
            if (
                $schema->hasProperty($propertyName)
                && count($configGroups) > 0
                && count($metaGroups = $metadata->getGroups()) > 0
                && count(array_intersect($metaGroups, $configGroups)) !== count($configGroups)
            ) {
                // Remove from parent and remove from parent config
                $schema->removeProperty($propertyName);
                $configRegistry->reduceConfig($schema, function (array $config) use ($propertyName) {
                    if (isset($config['properties'][$propertyName])) {
                        unset($config['properties'][$propertyName]);
                    }

                    return $config;
                });
            } else {
                $schemaProperty = $schema->getProperty($propertyName) ?: new Schema();
                $schema->addProperty($propertyName, $schemaProperty);
            }
        }
    }
}
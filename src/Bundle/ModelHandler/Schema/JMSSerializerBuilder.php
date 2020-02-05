<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema;

use Metadata\MetadataFactoryInterface;
use Metadata\ClassHierarchyMetadata;
use PhpSolution\SwaggerUIGen\Component\Model\Schema;

/**
 * Class JMSSerializerBuilder
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema
 */
class JMSSerializerBuilder implements SchemaBuilderInterface
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 30;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'jms_serializer';
    }

    /**
     * @param Schema         $schema
     * @param ConfigRegistry $configRegistry
     */
    public function buildSchema(Schema $schema, ConfigRegistry $configRegistry): void
    {
        $config = $configRegistry[$schema] ?? [];
        if (!isset($config['mapping']['class'])) {
            return;
        }

        $serializeMapping = $this->metadataFactory->getMetadataForClass($config['mapping']['class']);
        if ($serializeMapping instanceof ClassHierarchyMetadata) {
            foreach ($serializeMapping->classMetadata as $metadata) {
                $propertyName = $metadata->name;

                $schemaProperty = $schema->getProperty($propertyName) ?: new Schema();
                $schema->addProperty($propertyName, $schemaProperty);
            }
        }
    }
}
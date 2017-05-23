<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler;

use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema\ConfigRegistry;
use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema\SchemaBuilderInterface;
use PhpSolution\SwaggerUIGen\Component\Model\Schema;
use PhpSolution\SwaggerUIGen\Component\ModelHandler\GeneralFactory;

/**
 * Class SchemaFactory
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\ModelHandler
 */
class SchemaFactory
{
    private const COMPLEX_TYPES = ['array', 'object', 'collection'];
    private const TYPE_COLLECTION = 'collection';
    private const TYPE_OBJECT = 'object';

    /**
     * @var GeneralFactory
     */
    private $objectsFactory;
    /**
     * @var \ArrayObject|SchemaBuilderInterface[]
     */
    private $builders = [];
    /**
     * @var ConfigRegistry
     */
    private $modelConfigRegistry;

    /**
     * SchemaFactory constructor.
     *
     * @param GeneralFactory $objectsFactory
     */
    public function __construct(GeneralFactory $objectsFactory)
    {
        $this->objectsFactory = $objectsFactory;
        $this->modelConfigRegistry = new ConfigRegistry();
    }

    /**
     * @param SchemaBuilderInterface $builder
     */
    public function addBuilder(SchemaBuilderInterface $builder): void
    {
        $this->builders[$builder->getName()] = $builder;
        uasort(
            $this->builders,
            function (SchemaBuilderInterface $a, SchemaBuilderInterface $b) {
                return $a->getPriority() <=> $b->getPriority();
            }
        );
    }

    /**
     * @param array       $config
     * @param Schema|null $schema
     *
     * @return Schema
     */
    public function createSchemaObject(array $config, Schema $schema = null): Schema
    {
        $schema = $this->objectsFactory->createSchemaObject($config['openapi_params'] ?? [], $schema);
        if (array_key_exists('$ref', $config)) {
            $schema->setRef($config['$ref']);
        }
        $schema->setType($config['type'] ?? self::TYPE_OBJECT);
        if (in_array($schema->getType(), self::COMPLEX_TYPES)) {
            $this->buildComplexSchemaObject($config, $schema);
        }

        return $schema;
    }

    /**
     * Build by ObjectExtractConfig only for complex Schema objects
     *
     * @param array  $config
     * @param Schema $schema
     */
    private function buildComplexSchemaObject(array $config, Schema $schema): void
    {
        // Add config to Registry for use on builders
        $this->modelConfigRegistry[$schema] = $config;

        // Build current Schema object via builders
        $mappingBuilders = (array) $this->modelConfigRegistry->getMappingConfigValue($schema, 'type');
        foreach ($this->builders as $builder) {
            if (count($mappingBuilders) === 0 || in_array($builder->getName(), $mappingBuilders)) {
                $builder->buildSchema($schema, $this->modelConfigRegistry);
            }
        }

        // Config can be modified by builder
        $config = $this->modelConfigRegistry[$schema];
        // Build Schema Properties objects
        foreach ($config['properties'] ?? [] as $propertyName => $propertyConfig) {
            $propertySchema = $schema->getProperty($propertyName) ?: new Schema();
            $propertySchema->setParent($schema);
            $propertySchema = $this->createSchemaObject($propertyConfig, $propertySchema);
            $schema->addProperty($propertyName, $propertySchema);
        }

        // Handle correct properties for collection type
        if ($schema->getType() === self::TYPE_COLLECTION) {
            $this->buildCollectionSchema($schema);
        }
    }

    /**
     * @param Schema $schema
     */
    private function buildCollectionSchema(Schema $schema): void
    {
        $schemaItems = clone $schema;
        $schemaItems->setType(self::TYPE_OBJECT);
        $schemaItems->setProperties($schema->getProperties());
        $schemaItems->setParent($schema);
        $schema->setItems($schemaItems);
        $schema->setType('array');
        $schema->setProperties([]);
        $schema->setRef(null);
    }
}
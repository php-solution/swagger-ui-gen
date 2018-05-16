<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use PhpSolution\SwaggerUIGen\Component\Model\Schema;

/**
 * Class DoctrineBuilder
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema
 */
class DoctrineBuilder implements SchemaBuilderInterface
{
    private const ASSOC_TYPE_TRANSFORM = [
        ClassMetadata::ONE_TO_ONE => 'object',
        ClassMetadata::MANY_TO_ONE => 'object',
        ClassMetadata::MANY_TO_MANY => 'collection',
        ClassMetadata::ONE_TO_MANY => 'collection',
    ];

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * DoctrineBuilder constructor.
     *
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'doctrine';
    }

    /**
     * @param Schema         $schema
     * @param ConfigRegistry $configRegistry
     */
    public function buildSchema(Schema $schema, ConfigRegistry $configRegistry): void
    {
        $config = $configRegistry->offsetGet($schema);
        if (!isset($config['mapping']['class'])) {
            return;
        }

        $class = $config['mapping']['class'];
        $classManager = $this->doctrine->getManagerForClass($class);
        if ($classManager instanceof ObjectManager) {
            /* @var $classMetadata \Doctrine\ORM\Mapping\ClassMetadata */
            $classMetadata = $classManager->getClassMetadata($class);
            $this->buildMappingFields($schema, $classMetadata, $configRegistry);
            $this->buildMappingAssociations($schema, $classMetadata, $configRegistry);
        }
    }

    /**
     * @param Schema         $schema
     * @param ClassMetadata  $classMetadata
     * @param ConfigRegistry $configRegistry
     */
    private function buildMappingFields(Schema $schema, ClassMetadata $classMetadata, ConfigRegistry $configRegistry): void
    {
        foreach ($classMetadata->getFieldNames() as $fieldName) {
            $fieldMapping = $classMetadata->getFieldMapping($fieldName);
            $schemaProperty = $schema->getProperty($fieldName) ?: new Schema();
            $schemaProperty->setType($fieldMapping['type']);
            $schemaProperty->setUniqueItems(
                (array_key_exists('unique', $fieldMapping) && $fieldMapping['unique'])
                || (array_key_exists('id', $fieldMapping) && $fieldMapping['id'])
            );
            $schemaProperty->setMaxLength($fieldMapping['length'] ?? null);
            $schema->addProperty($fieldName, $schemaProperty);

            // Add config for run builders for property
            $propertyConfig = ['type' => $fieldMapping['type'],];
            $configRegistry->offsetSet($schemaProperty, $propertyConfig);
            $configRegistry->mergeTo($schema, ['properties' => [$fieldName => $propertyConfig]]);
        }
    }

    /**
     * @param Schema         $schema
     * @param ClassMetadata  $classMetadata
     * @param ConfigRegistry $configRegistry
     */
    private function buildMappingAssociations(Schema $schema, ClassMetadata $classMetadata, ConfigRegistry $configRegistry): void
    {
        foreach ($classMetadata->getAssociationMappings() as $fieldName => $associationMapping) {
            $schemaProperty = $schema->getProperty($fieldName) ?: new Schema('object');
            $schemaProperty->setTitle($fieldName);
            $schema->addProperty($fieldName, $schemaProperty);

            // Add config for run builders for property
            $propertyConfig = [
                'type' => self::ASSOC_TYPE_TRANSFORM[$associationMapping['type']],
                'mapping' => [
                    'class' => $associationMapping['targetEntity'],
                ],
            ];
            $configRegistry->offsetSet($schemaProperty, $propertyConfig);
            $configRegistry->mergeTo($schema, ['properties' => [$fieldName => $propertyConfig]]);
        }
    }
}
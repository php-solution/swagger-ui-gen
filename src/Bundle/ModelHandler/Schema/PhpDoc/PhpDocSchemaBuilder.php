<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema\PhpDoc;

use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema\ConfigRegistry;
use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema\SchemaBuilderInterface;
use PhpSolution\SwaggerUIGen\Component\Model\Schema;

/**
 * Class PhpDocSchemaBuilder
 */
class PhpDocSchemaBuilder implements SchemaBuilderInterface
{
    /**
     * @var PhpDocParserInterface
     */
    private $phpDocParser;

    /**
     * PhpDocSchemaBuilder constructor.
     *
     * @param PhpDocParserInterface $phpDocParser
     */
    public function __construct(PhpDocParserInterface $phpDocParser)
    {
        $this->phpDocParser = $phpDocParser;
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
        return 'phpdoc';
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

        $apiDoc = $this->phpDocParser->parse($config['mapping']['class']);
        $this->buildSchemaByApiDoc($schema, $apiDoc);
    }

    /**
     * @param Schema $schema
     * @param array  $apiDoc
     */
    private function buildSchemaByApiDoc(Schema $schema, array $apiDoc): void
    {
        foreach ($apiDoc as $propertyName => $propertyApiDoc) {
            $propertySchema = $schema->getProperty($propertyName) ?: new Schema();
            $propertySchema->setType($propertyApiDoc['type'] ?? 'string');
            $propertySchema->setDescription($propertyApiDoc['description'] ?? '');
            $propertySchema->setPattern($propertyApiDoc['pattern'] ?? null);
            $propertySchema->setFormat($propertyApiDoc['format'] ?? null);
            if (array_key_exists('enum', $propertyApiDoc) && is_array($propertyApiDoc['enum'])) {
                $propertySchema->setEnum($propertyApiDoc['enum']);
            }
            if (array_key_exists('children', $propertyApiDoc) && $propertyApiDoc['children']) {
                $this->buildSchemaByApiDoc($propertySchema, $propertyApiDoc['children']);
            }
            if ($propertySchema->getType() === 'array') {
                $propertySchema->setItems(new Schema('integer'));
            }
            if ($propertyApiDoc['required']) {
                $schema->addRequired($propertyName);
            }
            $schema->addProperty($propertyName, $propertySchema);
        }
    }
}
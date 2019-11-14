<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Swagger;

use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\SchemaFactory;
use PhpSolution\SwaggerUIGen\Component\DataNormalizer\DataNormalizerInterface;
use PhpSolution\SwaggerUIGen\Component\Model\OpenAPI;
use PhpSolution\SwaggerUIGen\Component\ModelHandler\SwaggerBuilderInterface;

/**
 * Class DefinitionsBuilder
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Swagger
 */
class DefinitionsBuilder implements SwaggerBuilderInterface, DataNormalizerInterface
{
    /**
     * @var SchemaFactory
     */
    private $schemaFactory;

    /**
     * DefinitionsBuilder constructor.
     *
     * @param SchemaFactory $schemaFactory
     */
    public function __construct(SchemaFactory $schemaFactory)
    {
        $this->schemaFactory = $schemaFactory;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    public function normalize(array $config): array
    {
        return isset($config['sf_object_definitions']) ? ['sf_object_definitions' => $config['sf_object_definitions']] : [];
    }

    /**
     * @param OpenAPI $swagger
     * @param array   $configs
     */
    public function build(OpenAPI $swagger, array $configs): void
    {
        foreach ($configs['sf_object_definitions'] ?? [] as $config) {
            $definition = $this->schemaFactory->createSchemaObject($config);
            $swagger->addSchemaToComponent($config['name'], $definition);
        }
    }
}
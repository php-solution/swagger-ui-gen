<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation;

use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\SchemaFactory;
use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use PhpSolution\SwaggerUIGen\Component\Model\Response;
use PhpSolution\SwaggerUIGen\Component\ModelHandler\GeneralFactory;

/**
 * Class GeneralBuilder
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation
 */
class GeneralBuilder implements OperationBuilderInterface
{
    /**
     * @var GeneralFactory
     */
    private $openapiObjectFactory;
    /**
     * @var SchemaFactory
     */
    private $schemaModelFactory;

    /**
     * GeneralBuilder constructor.
     *
     * @param GeneralFactory $generalFactory
     * @param SchemaFactory  $schemaFactory
     */
    public function __construct(GeneralFactory $generalFactory, SchemaFactory $schemaFactory)
    {
        $this->openapiObjectFactory = $generalFactory;
        $this->schemaModelFactory = $schemaFactory;
    }

    /**
     * @param Operation $operation
     * @param array     $config
     */
    public function build(Operation $operation, array $config): void
    {
        $operation->setSchemes($config['schemes'] ?? []);
        $operation->setTags($config['tags'] ?? []);
        $response = $this->createResponse($config['response'] ?? []);
        $operation->addResponse($config['response']['status_code'] ?? 'default', $response);
    }

    /**
     * @param array $config
     *
     * @return Response
     */
    private function createResponse(array $config): Response
    {
        $responseSchema = $this->schemaModelFactory->createSchemaObject($config);
        $response = $this->openapiObjectFactory->createResponseObject([]);
        $response->setSchema($responseSchema);

        return $response;
    }
}
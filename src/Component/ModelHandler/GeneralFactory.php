<?php

namespace PhpSolution\SwaggerUIGen\Component\ModelHandler;

use PhpSolution\SwaggerUIGen\Component\Model\Example;
use PhpSolution\SwaggerUIGen\Component\Model\ExternalDocumentation;
use PhpSolution\SwaggerUIGen\Component\Model\Header;
use PhpSolution\SwaggerUIGen\Component\Model\Items;
use PhpSolution\SwaggerUIGen\Component\Model\Operation;
use PhpSolution\SwaggerUIGen\Component\Model\Parameter;
use PhpSolution\SwaggerUIGen\Component\Model\ParameterGeneralInfo;
use PhpSolution\SwaggerUIGen\Component\Model\PathItem;
use PhpSolution\SwaggerUIGen\Component\Model\Response;
use PhpSolution\SwaggerUIGen\Component\Model\Schema;
use PhpSolution\SwaggerUIGen\Component\Model\SecurityRequirement;
use PhpSolution\SwaggerUIGen\Component\Model\SecurityScheme;
use PhpSolution\SwaggerUIGen\Component\Model\Tag;
use PhpSolution\SwaggerUIGen\Component\Utils\ObjectHydrator;

/**
 * Class GeneralFactory
 *
 * @package PhpSolution\SwaggerUIGen\Component\ModelHandler
 */
class GeneralFactory
{
    /**
     * @var ObjectHydrator
     */
    private $objectHydrator;

    /**
     * GeneralFactory constructor.
     *
     * @param ObjectHydrator|null $objectHydrator
     */
    public function __construct(ObjectHydrator $objectHydrator = null)
    {
        $this->objectHydrator = $objectHydrator ?: new ObjectHydrator();
    }

    /**
     * @param array $data
     *
     * @return Tag
     */
    public function createTagObject(array $data): Tag
    {
        $tag = new Tag();
        $tag->setName($data['name']);
        $tag->setDescription($data['description']);
        if (isset($data['externalDocs'])) {
            $tag->setExternalDocs($this->createExternalDocObject($data['externalDocs']));
        }

        return $tag;
    }

    /**
     * @param array $data
     *
     * @return PathItem
     */
    public function createPathItemObject(array $data): PathItem
    {
        $pathItem = new PathItem();
        $pathItem->setRef($data['$ref'] ?? null);
        foreach ($data['parameters'] ?? [] as $parameterConfig) {
            $pathItem->addParameter($this->createParameterObject($parameterConfig));
        }
        foreach (PathItem::OPERATION_NAMES as $methodName) {
            if (array_key_exists($methodName, $data)) {
                $pathItem->addOperation($methodName, $this->createOperationObject($data[$methodName]));
            }
        }

        return $pathItem;
    }

    /**
     * @param array $data
     *
     * @return Operation
     */
    public function createOperationObject(array $data): Operation
    {
        $operation = new Operation();
        $this->objectHydrator->hydrate(
            $operation,
            $data,
            ['tags', 'summary', 'description', 'operationId', 'consumes', 'produces', 'schemes', 'deprecated']
        );
        if (isset($data['externalDocs'])) {
            $operation->setExternalDocs($this->createExternalDocObject($data['externalDocs']));
        }
        foreach ($data['parameters'] ?? [] as $parameterConfig) {
            $operation->addParameter($this->createParameterObject($parameterConfig));
        }
        foreach ($data['responses'] ?? [] as $statusCode => $responseConfig) {
            $operation->addResponse($statusCode, $this->createResponseObject($responseConfig));
        }
        foreach ($data['security'] ?? [] as $securityConfig) {
            $operation->addSecurity($this->createSecurityRequirement($securityConfig));
        }

        return $operation;
    }

    /**
     * @param array $data
     *
     * @return Parameter
     */
    public function createParameterObject(array $data): Parameter
    {
        $parameter = new Parameter();
        $parameter->setName($data['name'] ?? '');
        $parameter->setRequired($data['required'] ?? false);
        $parameter->setDescription($data['description'] ?? null);
        if (isset($data['in'])) {
            $parameter->setIn($data['in']);
            if ($data['in'] === Parameter::IN_BODY) {
                $schema = $this->createSchemaObject($data['schema'] ?? []);
                $parameter->setSchema($schema);
            } else {
                $generalInfo = new ParameterGeneralInfo();
                $this->createItemsObject($data, $generalInfo);
                $parameter->setGeneralInfo($generalInfo);
            }
        }

        return $parameter;
    }

    /**
     * @param array      $data
     * @param Items|null $items
     *
     * @return Items
     */
    public function createItemsObject(array $data, Items $items = null): Items
    {
        $items = $items ?: new Items();
        $this->objectHydrator->hydrate(
            $items,
            $data,
            [
                'type',
                'format',
                'collectionFormat',
                'default',
                'maximum',
                'exclusiveMaximum',
                'minimum',
                'exclusiveMinimum',
                'maxLength',
                'minLength',
                'pattern',
                'maxItems',
                'minItems',
                'uniqueItems',
                'enum',
                'multipleOf',
            ]
        );
        if (isset($data['items'])) {
            $items->setItems($this->createItemsObject($data['items']));
        }

        return $items;
    }

    /**
     * @param array $data
     *
     * @return Response
     */
    public function createResponseObject(array $data): Response
    {
        $response = new Response();
        $response->setDescription($data['description'] ?? '');
        $response->setSchema(isset($data['schema']) ? $this->createSchemaObject($data['schema']) : null);
        $response->setExamples(isset($data['examples']) ? $this->createExampleObject($data['examples']) : null);
        foreach ($data['headers'] ?? [] as $headerKey => $headerConfig) {
            $response->addHeader($headerKey, $this->createHeaderObject($headerConfig));
        }

        return $response;
    }

    /**
     * @param array $data
     *
     * @return SecurityScheme
     */
    public function createSecuritySchemeObject(array $data): SecurityScheme
    {
        $securityScheme = new SecurityScheme();
        $this->objectHydrator->hydrateForIgnoredFields($securityScheme, $data, []);

        return $securityScheme;
    }

    /**
     * @param array $data
     *
     * @return SecurityRequirement
     */
    public function createSecurityRequirement(array $data): SecurityRequirement
    {
        $security = new SecurityRequirement();
        foreach ($data as $key => $value) {
            $security->addField($key, $value);
        }

        return $security;
    }

    /**
     * @param array $data
     *
     * @return ExternalDocumentation
     */
    public function createExternalDocObject(array $data): ExternalDocumentation
    {
        $externalDoc = new ExternalDocumentation();
        $externalDoc->setDescription($data['description'] ?? null);
        $externalDoc->setUrl($data['url'] ?? null);

        return $externalDoc;
    }

    /**
     * @param array       $data
     * @param Schema|null $schema
     *
     * @return Schema
     */
    public function createSchemaObject(array $data, Schema $schema = null): Schema
    {
        $schema = $schema ?: new Schema();
        $this->objectHydrator->hydrateForIgnoredFields(
            $schema,
            $data,
            ['externalDocs', 'additionalProperties', 'items', 'allOf', 'properties'],
            [],
            ['$ref' => 'ref']
        );

        if (isset($data['externalDocs'])) {
            $schema->setExternalDocs($this->createExternalDocObject($data['externalDocs']));
        }
        foreach ($data['additionalProperties'] ?? [] as $name => $itemData) {
            $schema->addAdditionalProperty($name, $this->createSchemaObject($itemData));
        }
        if (isset($data['items']['$ref'])) {
            $schemaRef = new Schema();
            $schemaRef->setRef($data['items']['$ref']);
            $schema->setItems($schemaRef);
        } elseif (isset($data['items'])) {
            $schema->setItems($this->createSchemaObject($data['items']));
        }
        foreach ($data['allOf'] ?? [] as $name => $itemData) {
            $schema->addAllOf($name, $this->createSchemaObject($itemData));
        }
        foreach ($data['properties'] ?? [] as $name => $itemData) {
            $schema->addProperty($name, $this->createSchemaObject($itemData));
        }

        return $schema;
    }

    /**
     * @param array $data
     *
     * @return Example
     */
    public function createExampleObject(array $data): Example
    {
        $example = new Example();
        foreach ($data as $key => $value) {
            $example->addEl($key, $value);
        }

        return $example;
    }

    /**
     * @param array $data
     *
     * @return Header
     */
    public function createHeaderObject(array $data): Header
    {
        $header = new Header();
        $this->createItemsObject($data, $header);
        $header->setDescription($data['description'] ?? null);
        return $header;
    }
}

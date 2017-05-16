<?php

namespace PhpSolution\SwaggerUIGen\Component\SchemaValidator;

use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator as SchemaValidator;

/**
 * Class SwaggerValidator
 *
 * @package PhpSolution\SwaggerUIGen\Component\SchemaValidator
 */
class SwaggerValidator
{
    private const SCHEMA_FILE = 'schema.json';
    private const SCHEMA = 'file://swagger';

    /**
     * @var SchemaStorage
     */
    private $schemaStorage;

    /**
     * @param array $data
     *
     * @return array
     */
    public function validate(array $data): array
    {
        $jsonToValidateObject = json_decode(json_encode($data));

        $validator = new SchemaValidator(new Factory($this->getSchemaStorage()));
        $validator->validate($jsonToValidateObject, $this->getSchemaStorage()->getSchema(static::SCHEMA));

        return array_map(
            function (array $error) {
                return sprintf("[%s] %s\n", $error['property'], $error['message']);
            },
            $validator->getErrors()
        );
    }

    /**
     * @return SchemaStorage
     */
    private function getSchemaStorage(): SchemaStorage
    {
        if (!$this->schemaStorage instanceof SchemaStorage) {
            $schemaPath = __DIR__ . DIRECTORY_SEPARATOR . static::SCHEMA_FILE;
            $jsonSchemaObject = json_decode(file_get_contents($schemaPath), true);

            $this->schemaStorage = new SchemaStorage();
            $this->schemaStorage->addSchema(static::SCHEMA, $jsonSchemaObject);
        }

        return $this->schemaStorage;
    }
}
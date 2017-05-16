<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema;

use PhpSolution\SwaggerUIGen\Component\Model\Schema;

/**
 * Interface SchemaBuilderInterface
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema
 */
interface SchemaBuilderInterface
{
    /**
     * @return int
     */
    public function getPriority(): int;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param Schema         $schema
     * @param ConfigRegistry $configRegistry
     *
     * @return mixed
     */
    public function buildSchema(Schema $schema, ConfigRegistry $configRegistry);
}
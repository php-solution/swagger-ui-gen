<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\PropertyNaming;

/**
 * Class NamingStrategyInterface
 */
interface NamingStrategyInterface
{
    /**
     * @param string $property
     *
     * @return string
     */
    public function getName(string $property): string;
}

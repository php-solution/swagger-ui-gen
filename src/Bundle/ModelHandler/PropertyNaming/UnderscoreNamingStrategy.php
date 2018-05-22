<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\PropertyNaming;

/**
 * Class UnderscoreNamingStrategy
 */
class UnderscoreNamingStrategy implements NamingStrategyInterface
{
    /**
     * @param string $property
     *
     * @return string
     */
    public function getName(string $property): string
    {
        return strtolower(
            preg_replace('/[A-Z]/',  '_\\0', $property)
        );
    }
}

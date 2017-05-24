<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema\PhpDoc;

/**
 * Class PhpDocParserInterface
 */
interface PhpDocParserInterface
{
    /**
     * @param string $className
     *
     * @return array
     */
    public function parse(string $className): array;
}
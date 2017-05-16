<?php

namespace PhpSolution\SwaggerUIGen\Component\DataProvider;

/**
 * Interface DataProviderInterface
 *
 * @package PhpSolution\SwaggerUIGen\Component\DataProvider
 */
interface DataProviderInterface
{
    /**
     * @return array
     */
    public function getData(): array;
}
<?php

namespace PhpSolution\SwaggerUIGen\Component\ModelHandler;

use PhpSolution\SwaggerUIGen\Component\Model\OpenAPI;

/**
 * Class SwaggerBuilderInterface
 *
 * @package PhpSolution\SwaggerUIGen\Component\ModelHandler
 */
interface SwaggerBuilderInterface
{
    /**
     * @param OpenAPI $model
     * @param array   $data
     *
     * @return mixed
     */
    public function build(OpenAPI $model, array $data);
}
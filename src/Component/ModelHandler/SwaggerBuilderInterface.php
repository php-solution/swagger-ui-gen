<?php

namespace PhpSolution\SwaggerUIGen\Component\ModelHandler;

use PhpSolution\SwaggerUIGen\Component\Model\Swagger;

/**
 * Class SwaggerBuilderInterface
 *
 * @package PhpSolution\SwaggerUIGen\Component\ModelHandler
 */
interface SwaggerBuilderInterface
{
    /**
     * @param Swagger $model
     * @param array   $data
     *
     * @return mixed
     */
    public function build(Swagger $model, array $data);
}
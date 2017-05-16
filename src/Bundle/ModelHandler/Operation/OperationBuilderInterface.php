<?php

namespace PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation;

use PhpSolution\SwaggerUIGen\Component\Model\Operation;

/**
 * Class OperationBuilderInterface
 *
 * @package PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation
 */
interface OperationBuilderInterface
{
    /**
     * @param Operation $operation
     * @param array     $data
     *
     * @return mixed
     */
    public function build(Operation $operation, array $data);
}
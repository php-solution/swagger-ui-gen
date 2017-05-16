<?php

namespace PhpSolution\SwaggerUIGen\Bundle;

use PhpSolution\SwaggerUIGen\Bundle\DependencyInjection\Pass\BuildersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class SwaggerUIGenBundle
 *
 * @package PhpSolution\SwaggerUIGen\Bundle
 */
class SwaggerUIGenBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new BuildersPass());
    }
}
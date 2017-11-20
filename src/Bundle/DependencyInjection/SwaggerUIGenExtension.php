<?php

namespace PhpSolution\SwaggerUIGen\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class SwaggerUIGenExtension
 *
 * @package PhpSolution\SwaggerUIGen\DependencyInjection
 */
class SwaggerUIGenExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('command.yml');

        $this->registerOptionsProvider($config, $container);
        $this->registerHandlers($config, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerOptionsProvider(array $config, ContainerBuilder $container): void
    {
        $container->getDefinition('swagger_uigen.data_provider')
            ->replaceArgument(0, $config['options_provider']['files'])
            ->replaceArgument(1, $config['options_provider']['folders'])
            ->replaceArgument(2, $config['options_provider']['defaults']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerHandlers(array $config, ContainerBuilder $container): void
    {
        if (!$config['handlers']['validator']) {
            $container->removeDefinition('swagger_uigen.model_builder.schema.validator');
        }
        if (!$config['handlers']['form_validator']) {
            $container->removeDefinition('swagger_uigen.model_builder.parameter.form_validator');
        }
        if (!$config['handlers']['form']) {
            $container->removeDefinition('swagger_uigen.model_builder.operation.form_type');
        }
        if (!$config['handlers']['serializer']) {
            $container->removeDefinition('swagger_uigen.model_builder.schema.serializer');
        }
        if (!$config['handlers']['doctrine_orm']) {
            $container->removeDefinition('swagger_uigen.model_builder.schema.doctrine');
        }
    }
}
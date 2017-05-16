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

        $this->registerOptionsProvider($config, $container);
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
}
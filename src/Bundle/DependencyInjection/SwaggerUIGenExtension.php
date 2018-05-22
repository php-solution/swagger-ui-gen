<?php

namespace PhpSolution\SwaggerUIGen\Bundle\DependencyInjection;

use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\FormTypeBuilder;
use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Operation\FormValidatorBuilder;
use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\Schema\{
    DoctrineBuilder,
    JMSSerializerBuilder,
    SerializerBuilder,
    ValidatorBuilder
};
use PhpSolution\SwaggerUIGen\Bundle\ModelHandler\SchemaFactory;
use PhpSolution\SwaggerUIGen\Component\DataProvider\DataProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * SwaggerUIGenExtension
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
        if ('test' === $container->getParameter('kernel.environment')) {
            $loader->load('services_test.yml');
        }

        $this->registerOptionsProvider($config, $container);
        $this->registerHandlers($config, $container);
        $this->registerNamingStrategy($config, $container);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerOptionsProvider(array $config, ContainerBuilder $container): void
    {
        $container->getDefinition(DataProviderInterface::class)
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
            $container->removeDefinition(ValidatorBuilder::class);
        }
        if (!$config['handlers']['form_validator']) {
            $container->removeDefinition(FormValidatorBuilder::class);
        }
        if (!$config['handlers']['form']) {
            $container->removeDefinition(FormTypeBuilder::class);
        }
        if (!$config['handlers']['serializer']) {
            $container->removeDefinition(SerializerBuilder::class);
        }
        if (!$config['handlers']['doctrine_orm']) {
            $container->removeDefinition(DoctrineBuilder::class);
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerNamingStrategy(array $config, ContainerBuilder $container): void
    {
        if ($config['naming_strategy_service']) {
            $container->getDefinition(SchemaFactory::class)
                ->setArgument(1, new Reference($config['naming_strategy_service']));
        }
    }
}

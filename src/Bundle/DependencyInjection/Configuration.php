<?php

namespace PhpSolution\SwaggerUIGen\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package PhpSolution\SwaggerUIGen\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('swagger_ui_gen');
        $this->addOptionsFilesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addOptionsFilesSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->arrayNode('options_provider')
                    ->children()
                        ->arrayNode('defaults')
                            ->canBeUnset()
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('files')
                            ->canBeUnset()
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('folders')
                            ->canBeUnset()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}

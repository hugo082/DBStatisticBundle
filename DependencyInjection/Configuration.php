<?php

namespace DB\StatisticBundle\DependencyInjection;

use DB\StatisticBundle\Core\Action\Action;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    public const TYPE_LINE = 'line';
    public const TYPE_BAR = 'bar';
    public const TYPE_RADAR = 'radar';
    public const TYPE_POLAR = 'polar';
    public const TYPE_PIE = 'pie';
    public const TYPE_DOUGHNUT = 'doughnut';
    public const TYPES = array(Configuration::TYPE_LINE, Configuration::TYPE_BAR, Configuration::TYPE_RADAR,
        Configuration::TYPE_POLAR, Configuration::TYPE_PIE, Configuration::TYPE_DOUGHNUT);

    public const ACTION_TYPES = array(Action::TYPE_SELECT, Action::TYPE_BUTTON);
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('db_statistic');

        $this->addBase($rootNode);
        $this->addEntitiesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addBase(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('service')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addEntitiesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('graphs')
                    ->prototype('array')->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('title')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('service')->end()  # Override
                            ->scalarNode('method')->isRequired()->cannotBeEmpty()->end()
                            ->enumNode('type')->isRequired()->cannotBeEmpty()
                                ->values(Configuration::TYPES)
                            ->end()
                            ->arrayNode('actions')
                                ->prototype('array')->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('id')->isRequired()->cannotBeEmpty()->end()
                                        ->enumNode('type')->isRequired()->cannotBeEmpty()
                                            ->values(Configuration::ACTION_TYPES)
                                        ->end()
                                        ->scalarNode('title')->end()
                                        ->arrayNode('choices')
                                            ->prototype('array')->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('id')->isRequired()->cannotBeEmpty()->end()
                                                    ->scalarNode('title')->isRequired()->cannotBeEmpty()->end()
                                                    ->scalarNode('default')->defaultValue('false')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}

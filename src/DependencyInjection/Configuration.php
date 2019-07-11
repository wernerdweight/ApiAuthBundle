<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use WernerDweight\ApiAuthBundle\Service\AccessScopeChecker\Checker\RouteChecker;

final class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->root('api_auth');

        $this->addConfiguration($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addConfiguration(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('client')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')
                            ->isRequired()
                        ->end()
                        ->scalarNode('property')
                            ->defaultNull()
                        ->end()
                        ->booleanNode('use_scope_access_model')
                            ->defaultFalse()
                        ->end()
                        ->scalarNode('access_scope_checker')
                            ->defaultValue(RouteChecker::class)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('user')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')
                            ->defaultNull()
                        ->end()
                        ->booleanNode('use_scope_access_model')
                            ->defaultFalse()
                        ->end()
                        ->scalarNode('access_scope_checker')
                            ->defaultValue(RouteChecker::class)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('target_controllers')
                    ->scalarPrototype()
                    ->end()
                ->end()
            ->end()
        ;
    }
}

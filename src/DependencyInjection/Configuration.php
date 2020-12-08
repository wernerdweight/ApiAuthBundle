<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use WernerDweight\ApiAuthBundle\Service\AccessScopeChecker\Checker\RouteChecker;

final class Configuration implements ConfigurationInterface
{
    /** @var string */
    private const DEFAULT_LOGIN_PROPERTY = 'username';
    /** @var int */
    private const DEFAULT_API_TOKEN_EXPIRATION_INTERVAL = 2592000;  // 30 days in seconds
    /** @var string */
    private const DEFAULT_CLIENT_PROPERTY = 'clientId';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('api_auth');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $this->addConfiguration($rootNode);

        return $treeBuilder;
    }

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
                            ->defaultValue(self::DEFAULT_CLIENT_PROPERTY)
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
                        ->scalarNode('token')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('login_property')
                            ->defaultValue(self::DEFAULT_LOGIN_PROPERTY)
                        ->end()
                        ->integerNode('api_token_expiration_interval')
                            ->defaultValue(self::DEFAULT_API_TOKEN_EXPIRATION_INTERVAL)
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
                ->booleanNode('exclude_options_requests')
                    ->defaultFalse()
                ->end()
            ->end()
        ;
    }
}

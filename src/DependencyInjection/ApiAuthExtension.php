<?php
declare(strict_types=1);

namespace WernerDweight\ApiAuthBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ApiAuthExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter(
            'api_auth.client.class',
            $config['client']['class']
        );
        $container->setParameter(
            'api_auth.client.property',
            $config['client']['property'] ?? null
        );
        $container->setParameter(
            'api_auth.client.use_scope_access_model',
            $config['client']['use_scope_access_model'] ?? false
        );
        $container->setParameter(
            'api_auth.client.access_scope_checker',
            $config['client']['access_scope_checker']
        );
        $container->setParameter(
            'api_auth.user.class',
            $config['user']['class'] ?? null
        );
        $container->setParameter(
            'api_auth.user.use_scope_access_model',
            $config['user']['use_scope_access_model'] ?? false
        );
        $container->setParameter(
            'api_auth.user.access_scope_checker',
            $config['user']['access_scope_checker']
        );
        $container->setParameter(
            'api_auth.target_controllers',
            $config['target_controllers'] ?? []
        );

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }
}

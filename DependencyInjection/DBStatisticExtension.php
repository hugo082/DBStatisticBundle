<?php

namespace DB\StatisticBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class DBStatisticExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->loadBase($config, $container);
        $this->loadGraphs($config, $container);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function loadBase(array $config, ContainerBuilder $container)
    {
        $container->setParameter($this->getAlias().'.service', $config['service']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function loadGraphs(array $config, ContainerBuilder $container)
    {
        foreach ($config['graphs'] as $name => &$values) {
            $values['id'] = $name;
            if (!isset($values['service']))
                $values['service'] = $config['service'];
        }
        $container->setParameter($this->getAlias().'.graphs', $config['graphs']);
    }
}

<?php

namespace HeimrichHannot\NewsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use HeimrichHannot\NewsBundle\DependencyInjection\Configuration;

class HeimrichHannotContaoNewsExtension extends Extension
{
    /**
     * @var array
     */
    private $files = [
        'services.yml',
        'commands.yml',
        'listener.yml'
    ];

    /**
     * {@inheritdoc}
     */
    public function load(array $mergedConfig, ContainerBuilder $container)
    {
        $configuration   = new Configuration(true);
        $processedConfig = $this->processConfiguration($configuration, $mergedConfig);
        $container->setParameter('social_stats', $processedConfig);

        $loader = new YamlFileLoader(
            $container, new FileLocator(__DIR__ . '/../Resources/config')
        );

        foreach ($this->files as $file) {
            $loader->load($file);
        }
    }
}

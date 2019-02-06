<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var bool
     */
    private $debug;

    /**
     * Constructor.
     *
     * @param bool $debug
     */
    public function __construct($debug)
    {
        $this->debug = (bool) $debug;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('social_stats');

        $rootNode
            ->children()
                ->scalarNode('chunksize')->defaultValue(20)->end()
                ->scalarNode('days')->defaultValue(180)->end()
                ->arrayNode('archives')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('facebook')->end()
                ->scalarNode('google_plus')->end()
                ->arrayNode('google_analytics')
                    ->children()
                        ->scalarNode('email')->end()
                        ->scalarNode('key_id')->end()
                        ->scalarNode('client_id')->end()
                        ->scalarNode('client_key')->end()
                        ->scalarNode('view_id')->end()
                        ->scalarNode('api_key')->end()
                        ->scalarNode('keyfile')
                            ->defaultValue('files/newsbundle/socialstats/google_analytics/privatekey.json')
                            ->end()
                    ->end()
                ->end()
                ->arrayNode('twitter')
                    ->children()
                        ->scalarNode('consumer_key')->end()
                        ->scalarNode('consumer_secret')->end()
                        ->scalarNode('access_token')->end()
                        ->scalarNode('access_token_secret')->end()
                    ->end()
                ->end()
                ->arrayNode('disqus')
                    ->children()
                        ->scalarNode('public_api_key')->end()
                        ->scalarNode('forum_name')->end()
                        ->scalarNode('identifier')->defaultValue('{id}')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}

<?php

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
        $rootNode    = $treeBuilder->root('social_stats');

        $rootNode
            ->children()
                ->scalarNode('chunksize')
                    ->defaultValue(0)
                ->end()
                ->scalarNode('google_sa_keyfile')
                ->end()
                ->scalarNode('google_sa_email')
                ->end()
                ->scalarNode('google_sa_client_id')
                ->end()
                ->scalarNode('google_analytics_profile_id')
                ->end()
                ->scalarNode('google_analytics_account_id')
                ->end()
                ->scalarNode('google_analytics_account_code_id')
                ->end()
                ->scalarNode('disqusPublicApiKey')
                ->end()
                ->scalarNode('disqusForumName')
                ->end()
                ->arrayNode('google_analytics')
                    ->children()
                        ->scalarNode('email')->end()
                        ->scalarNode('key_id')->end()
                        ->scalarNode('client_id')->end()
                        ->scalarNode('client_key')->end()
                        ->scalarNode('view_id')->end()
                        ->scalarNode('api_key')->end()
                        ->scalarNode('keyfile')
                            ->defaultValue('files/newsbundle/socialstats/google_analytics/client_secret.json')
                            ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
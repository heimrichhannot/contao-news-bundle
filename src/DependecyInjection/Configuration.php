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
            ->end();

        return $treeBuilder;
    }
}
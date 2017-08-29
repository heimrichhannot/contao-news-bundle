<?php

namespace HeimrichHannot\NewsBundle\ContaoManager;

use Codefog\TagsBundle\CodefogTagsBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Contao\NewsBundle\ContaoNewsBundle;
use HeimrichHannot\NewsBundle\HeimrichHannotContaoNewsBundle;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouteCollection;

class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(HeimrichHannotContaoNewsBundle::class)
                ->setLoadAfter([CodefogTagsBundle::class, ContaoNewsBundle::class])
        ];
    }

    /**
     * Returns a collection of routes for this bundle.
     *
     * @param LoaderResolverInterface $resolver
     * @param KernelInterface         $kernel
     *
     * @return null|RouteCollection
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        return $resolver
            ->resolve(__DIR__.'/../Resources/config/routing.yml')
            ->load(__DIR__.'/../Resources/config/routing.yml')
            ;
    }


}

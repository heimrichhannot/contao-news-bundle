<?php

namespace HeimrichHannot\NewsBundle\ContaoManager;

use HeimrichHannot\NewsBundle\ContaoNewsBundle;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ContaoNewsBundle::class)
                ->setLoadAfter([\Contao\NewsBundle\ContaoNewsBundle::class])
        ];
    }
}

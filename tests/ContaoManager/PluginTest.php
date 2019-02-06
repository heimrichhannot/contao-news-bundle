<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Test\ContaoManager;

use Codefog\TagsBundle\CodefogTagsBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\DelegatingParser;
use HeimrichHannot\CategoriesBundle\CategoriesBundle;
use HeimrichHannot\NewsBundle\ContaoManager\Plugin;
use HeimrichHannot\NewsBundle\HeimrichHannotContaoNewsBundle;
use PHPUnit\Framework\TestCase;

/**
 * Test the plugin class
 * Class PluginTest.
 */
class PluginTest extends TestCase
{
    public function testInstantiation()
    {
        static::assertInstanceOf(Plugin::class, new Plugin());
    }

    public function testGetBundles()
    {
        $plugin = new Plugin();

        /** @var BundleConfig[] $bundles */
        $bundles = $plugin->getBundles(new DelegatingParser());

        static::assertCount(1, $bundles);
        static::assertInstanceOf(BundleConfig::class, $bundles[0]);
        static::assertSame(HeimrichHannotContaoNewsBundle::class, $bundles[0]->getName());
        static::assertSame([CodefogTagsBundle::class, ContaoCoreBundle::class, CategoriesBundle::class], $bundles[0]->getLoadAfter());
    }
}

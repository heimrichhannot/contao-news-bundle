<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle;

use HeimrichHannot\NewsBundle\DependencyInjection\HeimrichHannotContaoNewsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotContaoNewsBundle extends Bundle
{
    const MODULE_NEWSLIST = 'newslist';
    const MODULE_NEWSNAVIGATION = 'newsnavigation';

    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * This method can be overridden to register compilation passes,
     * other extensions, ...
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new HeimrichHannotContaoNewsExtension();
    }
}

<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle;

use HeimrichHannot\NewsBundle\DependencyInjection\Compiler\FeedSourcePass;
use HeimrichHannot\NewsBundle\DependencyInjection\HeimrichHannotContaoNewsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotContaoNewsBundle extends Bundle
{
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
        $container->addCompilerPass(new FeedSourcePass());
    }


    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new HeimrichHannotContaoNewsExtension();
    }
}

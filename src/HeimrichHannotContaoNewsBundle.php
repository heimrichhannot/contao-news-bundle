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

use HeimrichHannot\NewsBundle\DependencyInjection\HeimrichHannotContaoNewsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotContaoNewsBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new HeimrichHannotContaoNewsExtension();
    }
}

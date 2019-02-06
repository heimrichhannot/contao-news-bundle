<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Test;

use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\NewsBundle\DependencyInjection\HeimrichHannotContaoNewsExtension;
use HeimrichHannot\NewsBundle\HeimrichHannotContaoNewsBundle;

class HeimrichHannotContaoNewsBundleTest extends ContaoTestCase
{
    public function testGetContainerExtension()
    {
        $bundle = new HeimrichHannotContaoNewsBundle();

        $this->assertInstanceOf(HeimrichHannotContaoNewsExtension::class, $bundle->getContainerExtension());
    }
}

<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Test\Backend;

use Contao\DataContainer;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\NewsBundle\Backend\News;

class NewsTest extends ContaoTestCase
{
    /**
     * @return DataContainer|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getDataContainerMock()
    {
        return $this->mockClassWithProperties(DataContainer::class, ['id' => 11]);
    }

    public function testGetRelatedNews()
    {
        $news = new News($this->mockContaoFramework([]));

        $this->assertNull($news->getRelatedNews(['value' => 11], $this->getDataContainerMock()));

        $this->assertSame(['value' => 12], $news->getRelatedNews(['value' => 12], $this->getDataContainerMock()));
    }

    public function testGetMembers()
    {
        $news = new News($this->mockContaoFramework([]));

        $this->assertNull($news->getMembers(['value' => 11], $this->getDataContainerMock()));

        $this->assertSame(['value' => 12], $news->getMembers(['value' => 12], $this->getDataContainerMock()));
    }
}

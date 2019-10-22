<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Test\ConfigElementType;

use Contao\FilesModel;
use Contao\PageModel;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\NewsBundle\ConfigElementType\NewsPlayerElementType;
use HeimrichHannot\NewsBundle\Model\NewsModel;
use HeimrichHannot\ReaderBundle\Item\DefaultItem;
use HeimrichHannot\ReaderBundle\Model\ReaderConfigElementModel;
use Model\Collection;
use Symfony\Bridge\Monolog\Logger;

class NewsPlayerElementTypeTest extends ContaoTestCase
{
    public function getNewsPlayerElementType(array $parameters = [], array $adapters = [])
    {
        if (!isset($parameters['container'])) {
            $parameters['container'] = $this->mockContainer();
        }
        if (!isset($parameters['framework'])) {
            $parameters['framework'] = $this->mockContaoFramework($adapters);
        }
        if (!$parameters['container']->has('monolog.logger.contao')) {
            $parameters['container']->set('monolog.logger.contao', $this->createMock(Logger::class));
        }
        $newsPlayerElementType = new NewsPlayerElementType($parameters['container'], $parameters['framework']);
        return $newsPlayerElementType;
    }

    public function testAddToItemData()
    {
        $newsModelAdapter = $this->mockAdapter(['findByPk']);
        $newsModelAdapter->method('findByPk')->willReturn(null);
        $newsPlayerElementType = $this->getNewsPlayerElementType([], [NewsModel::class => $newsModelAdapter]);
        $this->assertSame('', $newsPlayerElementType->addToItemData($this->getDefaultItem(), $this->getReaderConfigElementModel()));

        $newsModel = $this->mockClassWithProperties(NewsModel::class, ['player' => 'none']);
        $newsModelAdapter = $this->mockAdapter(['findByPk']);
        $newsModelAdapter->method('findByPk')->willReturn($newsModel);
        $newsPlayerElementType = $this->getNewsPlayerElementType([], [NewsModel::class => $newsModelAdapter]);
        $this->assertSame('', $newsPlayerElementType->addToItemData($this->getDefaultItem(), $this->getReaderConfigElementModel()));

        $newsModel = $this->mockClassWithProperties(NewsModel::class, ['player' => 'internal', 'playerSRC' => 'a:1:{i:0;s:16:"M��ƃU蹡@a�+�`";}']);
        $newsModelAdapter = $this->mockAdapter(['findByPk']);
        $newsModelAdapter->method('findByPk')->willReturn($newsModel);
        $newsPlayerElementType = $this->getNewsPlayerElementType([], [NewsModel::class => $newsModelAdapter]);
        $this->assertSame('', $newsPlayerElementType->addToItemData($this->getDefaultItem(), $this->getReaderConfigElementModel()));

        // to be continued...
//        $objPage = $this->mockClassWithProperties(PageModel::class, ['language' => 'de']);
//        global $objPage;
//        $newsModel        = $this->mockClassWithProperties(NewsModel::class, ['player' => 'internal', 'playerSRC' => serialize(['M��ƃU蹡@a�+�`'])]);
//        $newsModelAdapter = $this->mockAdapter(['findByPk']);
//        $newsModelAdapter->method('findByPk')->willReturn($newsModel);
//        $filesModel      = $this->mockClassWithProperties(FilesModel::class, ['extension' => 'mp4', 'meta' => '', 'name' => 'filesName', 'path' => '']);
//        $filesCollection = $this->mockClassWithProperties(Collection::class, ['arrModels' => [$filesModel]]);
//        $filesCollection->method('first')->willReturn($filesModel);
//        $filesCollection->method('reset')->willReturn(null);
//        $filesModelAdapter = $this->mockAdapter(['findMultipleByUuidsAndExtensions']);
//        $filesModelAdapter->method('findMultipleByUuidsAndExtensions')->willReturn($filesModel);
//        $newsPlayerElementType = new NewsPlayerElementType($this->mockContaoFramework([NewsModel::class => $newsModelAdapter]));
//        $this->assertSame('', $newsPlayerElementType->addToItemData($this->getDefaultItem(), $this->getReaderConfigElementModel()));
    }

    /**
     * @return DefaultItem|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getDefaultItem()
    {
        $defaultItem = $this->getMockBuilder(DefaultItem::class)->setMethods(['getRaw'])->disableOriginalConstructor()->getMock();
        $defaultItem->method('getRaw')->willReturn(['id' => 12]);

        return $defaultItem;
    }

    /**
     * @return ReaderConfigElementModel|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getReaderConfigElementModel()
    {
        return $this->mockClassWithProperties(ReaderConfigElementModel::class, []);
    }
}

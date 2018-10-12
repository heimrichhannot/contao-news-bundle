<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Test\Backend;

use Contao\DataContainer;
use Contao\System;
use Contao\TestCase\ContaoTestCase;
use HeimrichHannot\NewsBundle\Backend\CfgTag;
use HeimrichHannot\UtilsBundle\Dca\DcaUtil;
use HeimrichHannot\UtilsBundle\Model\CfgTagModel;
use HeimrichHannot\UtilsBundle\Model\ModelUtil;

class CfgTagTest extends ContaoTestCase
{
    public function testGenerateAlias()
    {
        $utilsModel = $this->createMock(ModelUtil::class);
        $utilsModel->method('findModelInstanceByPk')->willReturn(null);

        $container = $this->mockContainer();
        $container->set('huh.utils.model', $utilsModel);
        System::setContainer($container);

        $cfgTag = new CfgTag();

        $result = $cfgTag->generateAlias('test', $this->getDataContainerMock());
        $this->assertSame('test', $result);

        $token = $this->mockClassWithProperties(CfgTagModel::class, ['name' => 'test']);
        $utilsModel = $this->createMock(ModelUtil::class);
        $utilsModel->method('findModelInstanceByPk')->willReturn($token);

        $utilsDca = $this->createMock(DcaUtil::class);
        $utilsDca->method('generateAlias')->willReturn('success');

        $container = $this->mockContainer();
        $container->set('huh.utils.model', $utilsModel);
        $container->set('huh.utils.dca', $utilsDca);
        System::setContainer($container);

        $result = $cfgTag->generateAlias('test', $this->getDataContainerMock());
        $this->assertSame('success', $result);
    }

    /**
     * @return DataContainer|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getDataContainerMock()
    {
        return $this->mockClassWithProperties(DataContainer::class, ['id' => 11]);
    }
}

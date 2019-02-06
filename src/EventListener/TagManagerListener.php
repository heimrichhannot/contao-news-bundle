<?php

declare(strict_types=1);

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\EventListener;

use Codefog\TagsBundle\Manager\DcaAwareInterface;
use Codefog\TagsBundle\ManagerRegistry;
use Contao\DataContainer;
use HeimrichHannot\NewsBundle\Model\NewsModel;

class TagManagerListener
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * TagContainer constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * On the field save.
     *
     * @param string        $value
     * @param DataContainer $dc
     *
     * @return string
     */
    public function onFieldSave(string $value, DataContainer $dc): string
    {
        $manager = $this->registry->get($GLOBALS['TL_DCA'][$dc->table]['fields'][$dc->field]['eval']['tagsManager']);

        if ($manager instanceof DcaAwareInterface) {
            $value = $manager->saveDcaField($value, $dc);
        }

        $objModel = NewsModel::findByPk($dc->id);
        $objModel->{$dc->field} = deserialize($value, true);
        $objModel->save();

        return $value;
    }
}

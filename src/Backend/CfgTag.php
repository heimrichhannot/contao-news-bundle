<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Backend;

use Contao\DataContainer;
use Contao\System;
use HeimrichHannot\UtilsBundle\Model\CfgTagModel;

class CfgTag
{
    public function generateAlias($value, DataContainer $dc)
    {
        if (null === ($tag = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk(CfgTagModel::class, $dc->id))) {
            return $value;
        }

        return System::getContainer()->get('huh.utils.dca')->generateAlias($value, $dc->id, 'tl_cfg_tag', $tag->name, false);
    }
}

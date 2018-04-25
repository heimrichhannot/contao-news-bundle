<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Backend;

use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\UtilsBundle\Model\CfgTagModel;

class CfgTag extends \Backend
{
    public static function generateAlias($value, \DataContainer $dc)
    {
        if (null === ($tag = CfgTagModel::findByPk($dc->id))) {
            return $value;
        }

        return General::generateAlias($value, $dc->id, 'tl_cfg_tag', $tag->name, false);
    }
}

<?php

namespace HeimrichHannot\NewsBundle\Backend;

use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\UtilsBundle\Model\CfgTagModel;

class CfgTag extends \Backend
{
    public static function generateAlias($value, \DataContainer $dc)
    {
        if (($tag = CfgTagModel::findByPk($dc->id)) === null) {
            return $value;
        }

        return General::generateAlias($value, $dc->id, 'tl_cfg_tag', $tag->name, false);
    }
}
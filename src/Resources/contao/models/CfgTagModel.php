<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\NewsBundle\Model;


class CfgTagModel extends \Model
{
    protected static $strTable = 'tl_cfg_tag';

    public static function findAll(array $arrOptions = [])
    {
        return parent::findAll($arrOptions);
    }

    public static function findAllBySource($source, array $arrOptions = [])
    {
        return parent::findBy('source', $source, $arrOptions);
    }
}
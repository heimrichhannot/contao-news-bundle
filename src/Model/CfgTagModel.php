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

    /**
     * @param       $source
     * @param array $arrOptions
     *
     * @return @return static|Model\Collection|null A model, model collection or null if the result is empty
     */
    public static function findAllBySource($source, array $arrOptions = [])
    {
        return parent::findBy('source', $source, $arrOptions);
    }

    public static function findBy($column, $value, array $arrOptions = [])
    {
        return parent::findBy($column, $value, $arrOptions);
    }

    public static function getSourcesAsOptions(\DataContainer $dc)
    {
        $options = [];
        $tags    = \Database::getInstance()->prepare('SELECT source FROM tl_cfg_tag GROUP BY source')->execute();

        if ($tags !== null) {
            $options = $tags->fetchEach('source');

            asort($options);

        }

        return $options;
    }
}
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


use Codefog\TagsBundle\Model\TagModel;
use \Haste\Model\Model;

/**
 * Class NewsTagsModel
 *
 * @property integer $id;
 * @property integer $cfg_tag_id;
 * @property integer $news_id;
 *
 * @method static NewsTagsModel|null findById($id, $opt = [])
 * @method static NewsTagsModel|null findByPk($id, array $opt = [])
 * @method static \Model\Collection|NewsTagsModel[]|NewsTagsModel|null findMultipleByIds($val, $opt = [])
 * @method static \Model\Collection|NewsTagsModel[]|NewsTagsModel|null findBy($col, $val, $opt = [])
 * @method static \Model\Collection|NewsTagsModel[]|NewsTagsModel|null findAll($opt = [])
 *
 * @package HeimrichHannot\NewsBundle
 */
class NewsTagsModel extends Model
{
    protected static $strTable = 'tl_news_tags';

    /**
     * Find tags for a given news
     *
     * @param integer $varId The numeric news ID
     * @param array $arrOptions An optional options array
     *
     * @return \Model\Collection|NewsTagsModel[]|NewsTagsModel|null A collection of models or null if there are no tags
     */
    public static function findByNews($varId, array $arrOptions = [])
    {
        $t          = static::$strTable;
        $arrColumns = ["$t.news_id=?"];

        return static::findBy($arrColumns, $varId, $arrOptions);
    }
}
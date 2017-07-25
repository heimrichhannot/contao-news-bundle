<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\NewsBundle;


use \Haste\Model\Model;
use Model\Collection;
use HeimrichHannot\NewsBundle\NewsModel;

/**
 * Class NewsTagsModel
 *
 * @property integer $id;
 * @property integer $cfg_tag_id;
 * @property integer $news_id;
 *
 * @method static NewsTagsModel|null findById($id, $opt = [])
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
     * @param integer $id
     * @param array $opt
     *
     * @return \Model\Collection|NewsModel[]|NewsModel|null
     */
    public static function findNewsByTagId($id, $opt = [])
    {
        if (!is_int($id))
        {
            return null;
        }
        $objNewsTags = static::findBy('cfg_tag_id', $id, $opt);

        if ($objNewsTags === null)
        {
            return null;
        }

        $arrNewsIds = [];
        foreach ($objNewsTags as $entry)
        {
            $arrNewsIds[] = $entry->news_id;
        }
        $objNews = NewsModel::findMultipleByIds($arrNewsIds);
        return $objNews;
    }

}
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
//        if (!is_int($id))
//        {
//            return null;
//        }
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

    /**
     * @param integer|string $varId
     * @param array $arrOptions
     *
     * @return NewsTagsModel|null
     */
    public static function findTagsByIdOrAlias($varId, array $arrOptions = [])
    {
        $objTag = null;
        if (is_int($varId))
        {
            if ($objNewsTag = static::findBy('cfg_tag_id', $varId, $arrOptions) !== null)
            {
                $objTag = TagModel::findOneById($varId, $arrOptions);
            };

        }
        elseif (is_string($varId))
        {
            $objTag = TagModel::findOneByName($varId, $arrOptions);

            if ($objNewsTag = static::findBy('cfg_tag_id', $objTag->id, $arrOptions) === null)
            {
                return null;
            }
        }
        return $objTag;
    }

    /**
     * @param array $opt
     *
     * @return \Contao\Model\Collection|TagModel[]|TagModel|null
     */
    public static function findAllTags($opt = [])
    {
        $objNewsTags = static::findAll($opt);
        $arrTagIds = [];
        foreach ($objNewsTags as $entry)
        {
            if (!in_array($entry->cfg_tag_id, $arrTagIds))
            {
                $arrTagIds[] = $entry->cfg_tag_id;
            }
        }
        $objTags = TagModel::findMultipleByIds($arrTagIds);
        return $objTags;
    }

}
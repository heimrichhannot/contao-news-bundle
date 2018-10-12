<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Model;

use Contao\Model;

/**
 * Class NewsTagsModel.
 *
 * @property int $id          ;
 * @property int $cfg_tag_id;
 * @property int $news_id     ;
 *
 * @method static NewsTagsModel|null findById($id, $opt = [])
 * @method static NewsTagsModel|null findByPk($id, array $opt = [])
 * @method static \Model\Collection|NewsTagsModel[]|NewsTagsModel|null findMultipleByIds($val, $opt = [])
 * @method static \Model\Collection|NewsTagsModel[]|NewsTagsModel|null findBy($col, $val, $opt = [])
 * @method static \Model\Collection|NewsTagsModel[]|NewsTagsModel|null findAll($opt = [])
 */
class NewsTagsModel extends Model
{
    protected static $strTable = 'tl_news_tags';

    /**
     * Find tags for a given news.
     *
     * @param int   $varId      The numeric news ID
     * @param array $arrOptions An optional options array
     *
     * @return \Model\Collection|NewsTagsModel[]|NewsTagsModel|null A collection of models or null if there are no tags
     */
    public static function findByNews($varId, array $arrOptions = [])
    {
        $t = static::$strTable;
        $arrColumns = ["$t.news_id=?"];

        return static::findBy($arrColumns, $varId, $arrOptions);
    }
}

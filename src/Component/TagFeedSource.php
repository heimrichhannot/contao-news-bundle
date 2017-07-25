<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\NewsBundle\Component;


use Codefog\TagsBundle\Model\TagModel;
use HeimrichHannot\Haste\Model\Model;
use Model\Collection;
use HeimrichHannot\NewsBundle\NewsTagsModel;

class TagFeedSource implements FeedSourceInterface
{
    /**
     * Returns the type of the feed source, e.g. category, tag, collection,...
     * Will be used to create a subfolder in web/rss, to get paths like web/rss/category/my-category.xml
     *
     * @return string
     */
    public static function getType()
    {
        return 'tag';
    }

    /**
     * Return all rss channels that contain news entries, like a category, a tag, etc.
     * The channel my-category leads to my-category.xml
     * The channel must implement a name property.
     * @return Collection|Model|TagModel[]|TagModel|null
     */
    public static function getChannels()
    {
        return NewsTagsModel::findAllTags();
    }

    /**
     * @param Collection|Model|TagModel[]|TagModel $channel
     * @param integer $maxItems Max number of news to return
      *
     * @return Collection|Model|null
     */
    public static function getItemsByChannel($channel, $maxItems = 0)
    {
        $opt = [];
        if (is_int($maxItems) && $maxItems > 0)
        {
            $opt['limit'] = $maxItems;
        }
        return NewsTagsModel::findNewsByTagId($channel->id, $opt);
    }
}
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
use HeimrichHannot\ContaoNewsAlertBundle\Components\NewsTopicSourceInterface;
use HeimrichHannot\Haste\Model\Model;
use Model\Collection;
use HeimrichHannot\NewsBundle\NewsTagsModel;

class TagFeedSource implements FeedSourceInterface, NewsTopicSourceInterface
{

    private static $strCol = 'tags';

    /**
     * Return the label for the feed source
     *
     * @return array
     */
    public function getLabel()
    {
        return $GLOBALS['TL_LANG']['tl_news_feed']['source_tag'];
    }


    /**
     * Returns the type of the feed source, e.g. category, tag, collection,...
     * Will be used to create a subfolder in web/rss, to get paths like web/rss/category/my-category.xml
     *
     * @return string
     */
    public static function getAlias()
    {
        return static::$strCol;
    }

    /**
     * Returns a single news channel.
     * Channels are collections of news entries, e.g. a category, a tag, etc.
     * Channels should have an unique identifier and an unique alias
     * The channel My Category can lead to /share/category/my-category or /share/category/4 (if 4 is the id).
     *
     * @param string|integer $channel identifier or unique alias of the channel
     *
     * @return Collection|Model|null
     */
    public function getChannel($varId, $arrOptions = [])
    {
        return NewsTagsModel::findTagsByIdOrAlias($varId, $arrOptions);
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
        $objNews = NewsTagsModel::findNewsByTagId($channel->id, $opt);
        return $objNews;
    }

    /**
     * Returns the title of the channel.
     *
     * Return null, if channel not exist.
     *
     * @param Model $objChannel
     *
     * @return string|null
     */
    public static function getChannelTitle($objChannel)
    {
        $objTag = TagModel::findByIdOrAlias($objChannel->id);
        if ($objTag === null)
        {
            return null;
        }
        return $objTag->name;
    }

    /**
     * Return all available topics.
     *
     * @return array
     */
    public static function getTopics()
    {
        $objChannels = static::getChannels();
        $arrTopics = [];
        while ($objChannels->next())
        {
            $arrTopics[] = $objChannels->name;
        }
        return $arrTopics;
    }

    /**
     * Returns topics by news item
     *
     * @param $objItem \NewsModel
     *
     * @return array
     */
    public static function getTopicsByItem($objItem)
    {
        $objNewsTags = NewsTagsModel::findByNews_id($objItem->id);
        $arrTopics = [];
        if ($objNewsTags !== null)
        {
            while($objNewsTags->next())
            {
                $objTag = TagModel::findOneBy('id',$objNewsTags->cfg_tag_id);
                $arrTopics[] = $objTag->name;
            }
        }
        return $arrTopics;
    }

}
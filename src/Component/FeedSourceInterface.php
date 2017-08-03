<?php
/**
 * Created by PhpStorm.
 * User: tkoerner
 * Date: 25.07.17
 * Time: 11:37
 */

namespace HeimrichHannot\NewsBundle\Component;


use HeimrichHannot\Haste\Model\Model;
use HeimrichHannot\NewsBundle\NewsModel;
use Model\Collection;

interface FeedSourceInterface
{
    /**
     * Return the label for the feed source
     *
     * Example: $GLOBALS['TL_LANG']['tl_news_feed']['source_tag']
     *
     * @return string
     */
    public function getLabel();

    /**
     * Returns the alias of the feed source, e.g. category, tag, collection,...
     *
     * Will be used to create a route in web/share, to get paths like web/share/category/my-category.xml
     * Should be the database column in tl_news.
     * Should be unique.
     *
     * @return string
     */
    public static function getAlias();

    /**
     * Returns a single news channel.
     *
     * Channels are collections of news entries, e.g. a category, a tag, etc.
     * Channels should have an unique identifier and an unique alias
     * The channel My Category can lead to /share/category/my-category or /share/category/4 (if 4 is the id).
     *
     * @param string|integer $channel identifier or unique alias of the channel
     *
     * @return Collection|Model|null
     */
    public function getChannel($varChannel);

    /**
     * Return all available channels.
     *
     * Channels: see getChannel() doc
     *
     * @return Collection|Model|null
     */
    public static function getChannels();

    /**
     * Return news belonging to the channel
     *
     * @param Collection|Model $channel
     * @param integer $maxItems Max items to return. 0 = all items
     *
     * @return Collection|Model|NewsModel[]|NewsModel|null
     */
    public static function getItemsByChannel($objChannel, $maxItems = 0);

    /**
     * Returns the title of the channel.
     *
     * Return null, if channel not exist.
     *
     * @param Model $objChannel
     *
     * @return string|null
     */
    public static function getChannelTitle ($objChannel);

}
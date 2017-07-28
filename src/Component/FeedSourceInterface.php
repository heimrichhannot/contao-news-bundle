<?php
/**
 * Created by PhpStorm.
 * User: tkoerner
 * Date: 25.07.17
 * Time: 11:37
 */

namespace HeimrichHannot\NewsBundle\Component;


use HeimrichHannot\Haste\Model\Model;
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
     * Returns the type of the feed source, e.g. category, tag, collection,...
     * Will be used to create a subfolder in web/rss, to get paths like web/rss/category/my-category.xml
     *
     * @return string
     */
    public static function getType();

    /**
     * Return all rss channels that contain news entries, like a category, a tag, etc.
     * The channel my-category leads to my-category.xml
     *
     * @return Collection|Model|null
     */
    public static function getChannels();

    /**
     * @return Collection|Model|null
     */
//    public static function getItems();

    /**
     * @param Collection|Model $channel
     * @param integer $maxItems Max items to return. 0 = all items
     *
     * @return Collection|Model|null
     */
    public static function getItemsByChannel($channel, $maxItems = 0);

}
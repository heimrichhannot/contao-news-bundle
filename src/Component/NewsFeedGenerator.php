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
use Contao\Frontend;
use Contao\News;
use Contao\NewsFeedModel;
use Haste\Http\Response\XmlResponse;
use HeimrichHannot\Haste\Model\Model;
use HeimrichHannot\NewsBundle\NewsModel;
use HeimrichHannot\NewsBundle\NewsTagsModel;
use HeimrichHannot\NewsBundle\Component\FeedSourceInterface;
use Symfony\Component\HttpFoundation\Response;

class NewsFeedGenerator
{
    const FEEDGENERATION_DYNAMIC = 'dynamic';
    const FEEDGENERATION_XML = 'xml';


    /**
     * @var FeedSourceInterface[] $feedSource
     */
    protected $feedSource = [];
    protected $maxItems = 0;

    public function __construct()
    {
        $this->maxItems = 10;
    }

    /**
     * Add feed source
     *
     * @param \HeimrichHannot\NewsBundle\Component\FeedSourceInterface $source
     */
    public function addFeedSource(FeedSourceInterface $source)
    {
        $this->feedSource[$source->getType()] = $source;
    }

    /**
     * Get Feedsource by type
     *
     * @param string $key
     *
     * @return FeedSourceInterface|null
     */
    public function getFeedSource($key)
    {
        if (!isset($this->feedSource[$key]))
        {
            return null;
        }
        return $this->feedSource[$key];
    }

    public function getDcaSourceOptions ()
    {
        $options = [];
        foreach ($this->feedSource as $source)
        {
            $options[$source->getType()] = $source->getLabel();
        }
        return $options;
    }

    public function generateFeed($arrFeed)
    {
        $news = new \HeimrichHannot\NewsBundle\News();
        $objFeed = $news->generateDynamicFeed($arrFeed);
        return $objFeed->generateRss();
    }

    public function generateFeeds ()
    {
        /**
         * @var FeedSourceInterface $source
         */
        foreach ($this->feedSource as $source)
        {

            $channels = $source->getChannels();
            if ($channels === null)
            {
                break;
            }
            /**
             * @var TagModel $channel
             */
            foreach ($channels as $channel)
            {
                $items = $source->getItemsByChannel($channel, $this->maxItems);
                if ($items === null || $items->count() < 1)
                {
                    continue;
                }
                $name = !empty($channel->name) ? $channel->name : $source->getType().'_'.$channel->id;
                $arrFeed = [
                    'format' => 'rss',
                    'feedName' => $name,
                    'title' => $name,
                    'description' => null,
                    'language' => 'de',
                    'tstamp' => time(),
                    'source' => 'source_teaser',
                    'archive' => $items
                ];
                $this->generateFiles($arrFeed);
            }
        }
    }
}
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
    protected $feedSourceId = [];
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
        $this->feedSource[$source->getAlias()] = $source;
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
            $options[$source->getAlias()] = $source->getLabel();
        }
        return $options;
    }

    /**
     * @param array $arrFeed
     * @param string|int $varId Id oder unique alias of news source
     *
     * @return string|null
     */
    public function generateFeed($arrFeed, $varId=0)
    {
        $news = new \HeimrichHannot\NewsBundle\News();
        $objFeed = $news->generateDynamicFeed($arrFeed, $varId);
        $strFeed = $objFeed->generateRss();
        return $strFeed;
    }
}
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
use HeimrichHannot\Haste\Model\Model;
use HeimrichHannot\NewsBundle\NewsModel;
use HeimrichHannot\NewsBundle\NewsTagsModel;
use HeimrichHannot\NewsBundle\Component\FeedSourceInterface;

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

    public function addFeedSource(FeedSourceInterface $source)
    {
        $this->feedSource[] = $source;
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

    /**
     * Generate an XML files and save them to the root directory
     *
     * @see \Contao\News::generateFiles()
     *
     * @param array $arrFeed
     */
    protected function generateFiles($arrFeed)
    {

        $objArticle = $arrFeed['archive'];
        if (empty($objArticle))
        {
            return;
        }

        $strType = ($arrFeed['format'] == 'atom') ? 'generateAtom' : 'generateRss';
        $strLink = $arrFeed['feedBase'] ?: \Environment::get('base');
        $strFile = $arrFeed['feedName'];

        $objFeed = new \Feed($strFile);
        $objFeed->link = $strLink;
        $objFeed->title = $arrFeed['title'];
        $objFeed->description = $arrFeed['description'];
        $objFeed->language = $arrFeed['language'];
        $objFeed->published = $arrFeed['tstamp'];

        // Parse the items
        if ($objArticle !== null)
        {
            $arrUrls = array();

            while ($objArticle->next())
            {
                $jumpTo = $objArticle->getRelated('pid')->jumpTo;

                // No jumpTo page set (see #4784)
                if (!$jumpTo)
                {
                    continue;
                }

                // Get the jumpTo URL
                if (!isset($arrUrls[$jumpTo]))
                {
                    $objParent = \PageModel::findWithDetails($jumpTo);

                    // A jumpTo page is set but does no longer exist (see #5781)
                    if ($objParent === null)
                    {
                        $arrUrls[$jumpTo] = false;
                    }
                    else
                    {
                        $arrUrls[$jumpTo] = $objParent->getAbsoluteUrl(\Config::get('useAutoItem') ? '/%s' : '/items/%s');
                    }
                }

                // Skip the event if it requires a jumpTo URL but there is none
                if ($arrUrls[$jumpTo] === false && $objArticle->source == 'default')
                {
                    continue;
                }

                $strUrl = $arrUrls[$jumpTo];
                $objItem = new \FeedItem();

                $objItem->title = $objArticle->headline;
                $objItem->link =  $this->getLink($objArticle, $strUrl);
                $objItem->published = $objArticle->date;

                /** @var BackendUser $objAuthor */
                if (($objAuthor = $objArticle->getRelated('author')) !== null)
                {
                    $objItem->author = $objAuthor->name;
                }

                // Prepare the description
                if ($arrFeed['source'] == 'source_text')
                {
                    $strDescription = '';
                    $objElement = \ContentModel::findPublishedByPidAndTable($objArticle->id, 'tl_news');

                    if ($objElement !== null)
                    {
                        // Overwrite the request (see #7756)
                        $strRequest = \Environment::get('request');
                        \Environment::set('request', $objItem->link);

                        while ($objElement->next())
                        {
                            $strDescription .= News::getContentElement($objElement->current());
                        }

                        \Environment::set('request', $strRequest);
                    }
                }
                else
                {
                    $strDescription = $objArticle->teaser;
                }

                $strDescription = News::replaceInsertTags($strDescription, false);
                $objItem->description = News::convertRelativeUrls($strDescription, $strLink);

                // Add the article image as enclosure
                if ($objArticle->addImage)
                {
                    $objFile = \FilesModel::findByUuid($objArticle->singleSRC);

                    if ($objFile !== null)
                    {
                        $objItem->addEnclosure($objFile->path, $strLink);
                    }
                }

                // Enclosures
                if ($objArticle->addEnclosure)
                {
                    $arrEnclosure = \StringUtil::deserialize($objArticle->enclosure, true);

                    if (is_array($arrEnclosure))
                    {
                        $objFile = \FilesModel::findMultipleByUuids($arrEnclosure);

                        if ($objFile !== null)
                        {
                            while ($objFile->next())
                            {
                                $objItem->addEnclosure($objFile->path, $strLink);
                            }
                        }
                    }
                }

                $objFeed->addItem($objItem);
            }
        }

        // Create the file
        \File::putContent('web/rss/' . $strFile . '.xml', News::replaceInsertTags($objFeed->$strType(), false));
    }

    /**
     * Return the link of a news article
     *
     * @param NewsModel $objItem
     * @param string    $strUrl
     * @param string    $strBase
     *
     * @return string
     *
     * @see \Contao\News
     */
    protected function getLink($objItem, $strUrl, $strBase='')
    {
        switch ($objItem->source)
        {
            // Link to an external page
            case 'external':
                return $objItem->url;
                break;

            // Link to an internal page
            case 'internal':
                if (($objTarget = $objItem->getRelated('jumpTo')) instanceof PageModel)
                {
                    /** @var PageModel $objTarget */
                    return $objTarget->getAbsoluteUrl();
                }
                break;

            // Link to an article
            case 'article':
                if (($objArticle = \ArticleModel::findByPk($objItem->articleId, array('eager'=>true))) !== null && ($objPid = $objArticle->getRelated('pid')) instanceof PageModel)
                {
                    /** @var PageModel $objPid */
                    return ampersand($objPid->getAbsoluteUrl('/articles/' . ($objArticle->alias ?: $objArticle->id)));
                }
                break;
        }

        // Backwards compatibility (see #8329)
        if ($strBase != '' && !preg_match('#^https?://#', $strUrl))
        {
            $strUrl = $strBase . $strUrl;
        }

        // Link to the default page
        return sprintf($strUrl, ($objItem->alias ?: $objItem->id));
    }
}
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


use Contao\System;
use HeimrichHannot\NewsBundle\Component\NewsFeedGenerator;

class News extends \Contao\News
{

    const XHR_READER_SURVEY_RESULT_ACTION = 'showReadersSurveyResultAction';

    const XHR_GROUP = 'hh_news_bundle';

    const XHR_PARAMETER_ID    = 'id';
    const XHR_PARAMETER_ITEMS = 'items';

    /**
     * @param array      $arrFeed
     * @param string|int $varId ID or unique alias
     *
     * @return \Feed|null
     */
    public function generateDynamicFeed($arrFeed, $varId = 0)
    {
        $arrArchives = \StringUtil::deserialize($arrFeed['archives']);
        if (!is_array($arrArchives) || empty($arrArchives))
        {
            return null;
        }
        $strType = ($arrFeed['format'] == 'atom') ? 'generateAtom' : 'generateRss';
        $strLink = $arrFeed['feedBase'] ?: \Environment::get('base');
        $strFile = $arrFeed['feedName'];

        $objFeed              = new \Feed($strFile);
        $objFeed->link        = $strLink;
        $objFeed->title       = $arrFeed['title'];
        $objFeed->description = $arrFeed['description'];
        $objFeed->language    = $arrFeed['language'];
        $objFeed->published   = $arrFeed['tstamp'];

        // Get the items
        if ($arrFeed['maxItems'] > 0)
        {
            $objArticle = NewsModel::findPublishedByNewsSource($arrFeed['news_source'], $varId, $arrFeed['maxItems'], 0, ['news_source' => $arrFeed['news_source']]);
        }
        else
        {
            $objArticle = NewsModel::findPublishedByNewsSource($arrFeed['news_source'], $varId, 0, 0, ['news_source' => $arrFeed['news_source']]);
        }

        // Parse the items
        if ($objArticle !== null)
        {
            $arrUrls = [];
            while ($objArticle->next())
            {
                $strNewsSource = $arrFeed['news_source'];
                if ($objArticle->$strNewsSource === null)
                {
                    continue;
                }
                if (empty(deserialize($objArticle->$strNewsSource)))
                {
                    continue;
                }
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
                $strUrl             = $arrUrls[$jumpTo];
                $objItem            = new \FeedItem();
                $objItem->title     = $objArticle->headline;
                $objItem->link      = $this->getLink($objArticle, $strUrl);
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
                    $objElement     = \ContentModel::findPublishedByPidAndTable($objArticle->id, 'tl_news');
                    if ($objElement !== null)
                    {
                        // Overwrite the request (see #7756)
                        $strRequest = \Environment::get('request');
                        \Environment::set('request', $objItem->link);
                        while ($objElement->next())
                        {
                            $strDescription .= $this->getContentElement($objElement->current());
                        }
                        \Environment::set('request', $strRequest);
                    }
                }
                else
                {
                    $strDescription = $objArticle->teaser;
                }
                $strDescription       = $this->replaceInsertTags($strDescription, false);
                $objItem->description = $this->convertRelativeUrls($strDescription, $strLink);
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

        return $objFeed;
    }

    /**
     * Generate an XML files and save them to the root directory
     *
     * @param array
     */
    protected function generateFiles($arrFeed)
    {
        // Don't generate xml-files for dynamic feeds
        if ($arrFeed["feedGeneration"] !== NewsFeedGenerator::FEEDGENERATION_DYNAMIC)
        {
            return parent::generateFiles($arrFeed);
        }
    }

}
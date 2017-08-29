<?php

namespace HeimrichHannot\NewsBundle;


use HeimrichHannot\NewsBundle\Model\NewsListArchiveModel;
use HeimrichHannot\NewsBundle\Model\NewsListModel;

class Hooks
{
    /**
     * Modify the page or layout object
     *
     * @param \PageModel $objPage
     * @param \LayoutModel $objLayout
     * @param \PageRegular $objPageRegular
     */
    public function getPageLayoutHook(\PageModel $objPage, \LayoutModel $objLayout, \PageRegular $objPageRegular)
    {
        NewsList::resetSeen($objPage->id); // reset seen news articles on request
    }

    /**
     * Extend the news list count all items
     *
     * @param array $newsArchives
     * @param bool|null $blnFeatured
     * @param \Module $objModule
     *
     * @return int|boolean Return the number of total items or false if next hook should be triggered
     */
    public function newsListCountItemsHook(array $newsArchives, $blnFeatured, \Module $objModule)
    {
        $objNewsList = new NewsList($newsArchives, $blnFeatured, $objModule);

        return $objNewsList->count();
    }


    /**
     * Extend fetch matching of news list items
     *
     * @param  array $newsArchives
     * @param  boolean|null $blnFeatured
     * @param  integer $limit
     * @param  integer $offset
     * @param \Module $objModule
     *
     * @return \Model\Collection|NewsModel|null|false Return a collection of items or false if next hook should be triggered
     */
    public function newsListFetchItemsHook(array $newsArchives, $blnFeatured, $limit, $offset, \Module $objModule)
    {
        $objNewsList = new NewsList($newsArchives, $blnFeatured, $objModule);

        return $objNewsList->fetch($limit, $offset);
    }


    /**
     * Extend news article data
     *
     * @param \FrontendTemplate $template
     * @param array $article
     * @param \Module $module
     */
    public function parseArticleHook(\FrontendTemplate $template, array $article, \Module $module)
    {
        $objArticle = new NewsArticle($template, $article, $module);
        $template   = $objArticle->getNewsTemplate();
    }

    /**
     * Add news items to the indexer
     *
     * @param array $pages
     * @param integer $rootId
     * @param boolean $isSitemap
     *
     * @return array
     */
    public function getSearchablePages($pages, $rootId = 0, $isSitemap = false)
    {
        $root = [];

        if ($rootId > 0) {
            $root = \Database::getInstance()->getChildRecords($rootId, 'tl_page');
        }

        $processed = [];
        $time      = \Date::floorToMinute();

        if (($archive = NewsListArchiveModel::findAll()) !== null) {
            while ($archive->next()) {
                if (!$archive->jumpTo || !empty($root) && !in_array($archive->jumpTo, $root)) {
                    continue;
                }

                if (!isset($processed[$archive->jumpTo])) {
                    if (($parent = \PageModel::findWithDetails($archive->jumpTo)) === null) {
                        continue;
                    }

                    if (!$parent->published || ($parent->start != '' && $parent->start > $time) || ($parent->stop != '' && $parent->stop <= ($time + 60))) {
                        continue;
                    }

                    if ($isSitemap) {
                        if ($parent->protected) {
                            continue;
                        }

                        if ($parent->sitemap == 'map_never') {
                            continue;
                        }
                    }

                    // Generate the URL
                    $processed[$archive->jumpTo] = $parent->getAbsoluteUrl(\Config::get('useAutoItem') ? '/%s' : '/items/%s');
                }

                $url = $processed[$archive->jumpTo];

                if (($newsList = NewsListModel::findBy(['pid=?', 'published=?'], [$archive->id, true])) !== null) {
                    while ($newsList->next()) {
                        $pages[] = sprintf($url, $newsList->alias ?: $url->id);
                    }
                }
            }
        }

        return $pages;
    }

}
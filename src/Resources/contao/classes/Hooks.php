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
}
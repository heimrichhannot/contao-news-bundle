<?php

namespace HeimrichHannot\NewsBundle;


class Hooks
{

    /**
     * Extend the news list count all items
     *
     * @param array   $newsArchives
     * @param bool    $blnFeatured
     * @param \Module $objModule
     *
     * @return int|boolean Return the number of total items or false if next hook should be triggered
     */
    public function newsListCountItemsHook(array $newsArchives, bool $blnFeatured, \Module $objModule)
    {
        $objNewsList = new NewsList($newsArchives, $blnFeatured, $objModule);

        return $objNewsList->count();
    }


    /**
     * Extend fetch matching of news list items
     *
     * @param  array   $newsArchives
     * @param  boolean $blnFeatured
     * @param  integer $limit
     * @param  integer $offset
     * @param \Module  $objModule
     *
     * @return \Model\Collection|NewsModel|null|false Return a collection of items or false if next hook should be triggered
     */
    public function newsListFetchItemsHook(array $newsArchives, bool $blnFeatured, $limit, $offset, \Module $objModule)
    {
        $objNewsList = new NewsList($newsArchives, $blnFeatured, $objModule);

        return $objNewsList->fetch($limit, $offset);
    }


    /**
     * Extend news article data
     *
     * @param \FrontendTemplate $objTemplate
     * @param array             $arrArticle
     * @param \Module           $objModule
     */
    public function parseArticleHook(\FrontendTemplate $objTemplate, array $arrArticle, \Module $objModule)
    {
        $objArticle  = new NewsArticle($objTemplate, $arrArticle);
        $objTemplate = $objArticle->getTemplate();
    }

}
<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Module;

use HeimrichHannot\NewsBundle\Model\NewsModel;

class ModuleNewsListRelated extends \ModuleNewsList
{
    /**
     * The parent news that contains the relations.
     *
     * @var \NewsModel
     */
    protected $news;

    public function generate()
    {
        if (TL_MODE == 'FE' && !$this->news) {
            $this->news_archives = $this->sortOutProtected(\StringUtil::deserialize($this->news_archives));

            // Return if there are no archives
            if (empty($this->news_archives) || !\is_array($this->news_archives)) {
                return '';
            }

            // Get the news item from auto_item
            if (null === ($this->news = \NewsModel::findPublishedByParentAndIdOrAlias(\Input::get('items'), $this->news_archives))) {
                return '';
            }
        }

        $this->news->related_news = deserialize($this->news->related_news, true);

        return parent::generate();
    }

    /**
     * Get the parent news id.
     *
     * @return int
     */
    public function getNews(): int
    {
        return $this->news->id;
    }

    /**
     * Get the parent news.
     *
     * @return \NewsModel
     */
    public function getNewsModel()
    {
        return $this->news;
    }

    /**
     * Set the parent news id.
     *
     * @param int $news
     */
    public function setNews(int $news)
    {
        if (null !== ($model = NewsModel::findByPk($news))) {
            $this->news = $model;
        }
    }

    /**
     * Count the total matching items.
     *
     * @param array $newsArchives
     * @param bool  $blnFeatured
     *
     * @return int
     */
    protected function countItems($newsArchives, $blnFeatured)
    {
        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['newsListRelatedCountItems']) && is_array($GLOBALS['TL_HOOKS']['newsListRelatedCountItems'])) {
            foreach ($GLOBALS['TL_HOOKS']['newsListRelatedCountItems'] as $callback) {
                if (false === ($intResult = \System::importStatic($callback[0])->{$callback[1]}($newsArchives, $this->news, $blnFeatured, $this))) {
                    continue;
                }

                if (is_int($intResult)) {
                    return $intResult;
                }
            }
        }

        $options = [];

        if (!empty($this->news->related_news)) {
            $options = ['order' => 'FIELD(tl_news.id, '.implode(',', array_map('intval', $this->news->related_news)).')'];

            return NewsModel::countPublishedByPidsAndIds($newsArchives, $this->news->related_news, $blnFeatured, $options);
        }

        return NewsModel::countPublishedByPids($newsArchives, $blnFeatured, $options);
    }

    /**
     * Fetch the matching items.
     *
     * @param array $newsArchives
     * @param bool  $blnFeatured
     * @param int   $limit
     * @param int   $offset
     *
     * @return \Model\Collection|\NewsModel|null
     */
    protected function fetchItems($newsArchives, $blnFeatured, $limit, $offset)
    {
        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['newsListRelatedFetchItems']) && is_array($GLOBALS['TL_HOOKS']['newsListRelatedFetchItems'])) {
            foreach ($GLOBALS['TL_HOOKS']['newsListRelatedFetchItems'] as $callback) {
                if (false === ($objCollection = \System::importStatic($callback[0])->{$callback[1]}($newsArchives, $this->news, $blnFeatured, $limit, $offset, $this))) {
                    continue;
                }

                if (null === $objCollection || $objCollection instanceof \Model\Collection) {
                    return $objCollection;
                }
            }
        }

        $options = [];

        if (!empty($this->news->related_news)) {
            $options = ['order' => 'FIELD(tl_news.id, '.implode(',', array_map('intval', $this->news->related_news)).')'];

            return NewsModel::findPublishedByPidsAndIds($newsArchives, $this->news->related_news, $blnFeatured, $limit, $offset, $options);
        }

        return NewsModel::findPublishedByPids($newsArchives, $blnFeatured, $limit, $offset, $options);
    }
}

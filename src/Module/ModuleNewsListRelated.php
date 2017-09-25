<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\Module;

use HeimrichHannot\NewsBundle\Model\NewsModel;

class ModuleNewsListRelated extends \ModuleNewsList
{
    /**
     * The parent news that contains the relations
     *
     * @var \NewsModel
     */
    protected $news;

    public function generate()
    {
        if (TL_MODE == 'FE' && !$this->news) {
            return '';
        }

        $this->news->related_news = deserialize($this->news->related_news, true);

        return parent::generate();
    }

    /**
     * Count the total matching items
     *
     * @param array $newsArchives
     * @param boolean $blnFeatured
     *
     * @return integer
     */
    protected function countItems($newsArchives, $blnFeatured)
    {
        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['newsListRelatedCountItems']) && is_array($GLOBALS['TL_HOOKS']['newsListRelatedCountItems'])) {
            foreach ($GLOBALS['TL_HOOKS']['newsListRelatedCountItems'] as $callback) {
                if (($intResult = \System::importStatic($callback[0])->{$callback[1]}($newsArchives, $this->news, $blnFeatured, $this)) === false) {
                    continue;
                }

                if (is_int($intResult)) {
                    return $intResult;
                }
            }
        }

        $options = [];

        if (!empty($this->news->related_news))
        {
            $options = ['order' => "FIELD(tl_news.id, " . implode(',', array_map('intval', $this->news->related_news)) . ")"];
            return NewsModel::countPublishedByPidsAndIds($newsArchives, $this->news->related_news, $blnFeatured, $options);
        }

        return NewsModel::countPublishedByPids($newsArchives, $blnFeatured, $options);
    }


    /**
     * Fetch the matching items
     *
     * @param  array $newsArchives
     * @param  boolean $blnFeatured
     * @param  integer $limit
     * @param  integer $offset
     *
     * @return \Model\Collection|\NewsModel|null
     */
    protected function fetchItems($newsArchives, $blnFeatured, $limit, $offset)
    {
        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['newsListRelatedFetchItems']) && is_array($GLOBALS['TL_HOOKS']['newsListRelatedFetchItems'])) {
            foreach ($GLOBALS['TL_HOOKS']['newsListRelatedFetchItems'] as $callback) {
                if (($objCollection = \System::importStatic($callback[0])->{$callback[1]}($newsArchives, $this->news, $blnFeatured, $limit, $offset, $this)) === false) {
                    continue;
                }

                if ($objCollection === null || $objCollection instanceof \Model\Collection) {
                    return $objCollection;
                }
            }
        }

        $options = [];

        if (!empty($this->news->related_news))
        {
            $options = ['order' => "FIELD(tl_news.id, " . implode(',', array_map('intval', $this->news->related_news)) . ")"];
            return NewsModel::findPublishedByPidsAndIds($newsArchives, $this->news->related_news, $blnFeatured, $limit, $offset, $options);
        }

        return NewsModel::findPublishedByPids($newsArchives, $blnFeatured, $limit, $offset, $options);
    }

    /**
     * Get the parent news id
     *
     * @return int
     */
    public function getNews(): int
    {
        return $this->news->id;
    }

    /**
     * Get the parent news
     * @return \NewsModel
     */
    public function getNewsModel()
    {
        return $this->news;
    }

    /**
     * Set the parent news id
     *
     * @param int $news
     */
    public function setNews(int $news)
    {
        if (($model = NewsModel::findByPk($news)) !== null) {
            $this->news = $model;
        }
    }
}
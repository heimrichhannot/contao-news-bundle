<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle;


use HeimrichHannot\FieldPalette\FieldPaletteModel;
use HeimrichHannot\NewsBundle\Manager\NewsTagManager;
use HeimrichHannot\NewsBundle\Model\NewsListModel;
use HeimrichHannot\NewsBundle\Model\NewsTagsModel;
use NewsCategories\NewsCategories;
use NewsCategories\NewsCategoryModel;

class NewsList
{

    const SESSION_SEEN_NEWS = 'SESSION_SEEN_NEWS';

    /**
     * List of news archive ids
     *
     * @var array
     */
    protected $newsArchives = [];

    /**
     * Set true if only featured items should be handled
     *
     * @var bool
     */
    protected $featured;

    /**
     * Current front end module instance
     *
     * @var \Module
     */
    protected $module;

    /**
     * News table
     *
     * @var string
     */
    protected static $table = 'tl_news';

    /**
     * Filter statement columns
     *
     * @var array
     */
    protected $filterColumns = [];

    /**
     * Filter statement values
     *
     * @var null|array
     */
    protected $filterValues = null;

    /**
     * Filter statement options
     *
     * @var array
     */
    protected $filterOptions = [];

    /**
     * NewsList constructor.
     *
     * @param array $newsArchives
     * @param bool|null $featured
     * @param \Module $module
     */
    public function __construct(array $newsArchives, $featured, \Module $module)
    {
        $this->newsArchives = $newsArchives;
        $this->featured     = $featured;
        $this->module       = $module;
    }

    /**
     * Count news items
     *
     * @return integer|boolean Return the number of news items of false for default count behavior
     */
    public function count()
    {
        return NewsModel::countPublishedByPidsAndCallback($this->newsArchives, [$this, 'extendCount'], $this->featured, []);
    }

    /**
     * Extend count news items statement
     *
     * @param array $columns
     * @param array|null $values
     * @param array $options
     */
    public function extendCount(array &$columns, &$values, array &$options)
    {
        $this->filterColumns = $columns;
        $this->filterValues  = $values;
        $this->filterOptions = $options;

        $this->addCountFilters();

        $columns = $this->filterColumns;
        $values  = $this->filterValues;
        $options = $this->filterOptions;
    }

    protected function addCountFilters()
    {
        $this->addNewsListFilter();
        $this->addSkipPreviousNewsFilter();
        $this->addCategoryFilter();
        $this->addTagFilter();
    }

    /**
     * Fetch news items
     *
     * @param integer $limit Current limit from pagination
     * @param integer $offset Current offset from pagination
     *
     * @return \Model\Collection|NewsModel|null|boolean Return a collection it news items of false for the default fetch behavior
     */
    public function fetch($limit, $offset)
    {
        return NewsModel::findPublishedByPidsAndCallback($this->newsArchives, [$this, 'extendFetch'], $this->featured, $limit, $offset, []);
    }


    /**
     * Extend fetch news items statement
     *
     * @param array $columns
     * @param array|null $values
     * @param array $options
     */
    public function extendFetch(array &$columns, &$values, array &$options)
    {
        $this->filterColumns = $columns;
        $this->filterValues  = $values;
        $this->filterOptions = $options;

        $this->addFetchFilters();

        $columns = $this->filterColumns;
        $values  = $this->filterValues;
        $options = $this->filterOptions;
    }

    protected function addFetchFilters()
    {
        $this->addNewsListFilter();
        $this->addSkipPreviousNewsFilter();
        $this->addCategoryFilter();
        $this->addTagFilter();
    }

    private function addSkipPreviousNewsFilter()
    {
        if ($this->module->skipPreviousNews && ($skipIds = static::getSeen()) !== null) {
            $t                     = static::$table;
            $this->filterColumns[] = "$t.id NOT IN(" . implode(',', array_map('intval', $skipIds)) . ")";
        }
    }

    private function addNewsListFilter()
    {
        if ($this->module->use_news_lists) {
            $t = static::$table;

            switch ($this->module->newsListMode) {
                case \HeimrichHannot\NewsBundle\Backend\NewsList::MODE_MANUAL:
                    $relations = FieldPaletteModel::findPublishedByPidsAndTableAndField(deserialize($this->module->news_lists, true), 'tl_news_list', 'news');

                    if ($relations === null) {
                        return false;
                    }

                    $ids = $relations->fetchEach('news_list_news');

                    break;
                case \HeimrichHannot\NewsBundle\Backend\NewsList::MODE_AUTO_ITEM:
                    // Set the item from the auto_item parameter
                    if (!isset($_GET['news_list']) && \Config::get('useAutoItem') && isset($_GET['auto_item'])) {
                        $alias = \Input::get('auto_item');

                        \Input::setGet('news_list', $alias);
                    }

                    if (!\Input::get('news_list')) {
                        \Controller::redirect('/');
                    }

                    if (($objNewsList = NewsListModel::findBy(['alias=?', 'published=?'], [\Input::get('news_list'), true])) !== null) {
                        $relations = FieldPaletteModel::findPublishedByPidsAndTableAndField([$objNewsList->id], 'tl_news_list', 'news');

                        if ($relations === null) {
                            return false;
                        }

                        $ids = $relations->fetchEach('news_list_news');
                    }
                    break;
            }

            if (!empty($ids)) {
                $this->filterColumns[]        = "$t.id IN(" . implode(',', array_map('intval', $ids)) . ")";
                $this->filterOptions['order'] = "FIELD($t.pid, " . implode(',', array_map('intval', $this->newsArchives)) . "), FIELD($t.id, " . implode(',', array_map('intval', $ids)) . ")";
            }
        }
    }

    private function addTagFilter()
    {
        if ($this->module->addNewsTagFilter) {
            $t = static::$table;

            if (!isset($_GET['news_tag']) && \Config::get('useAutoItem') && isset($_GET['auto_item'])) {
                $alias = \Input::get('auto_item');

                \Input::setGet('news_tag', $alias);
            }

            if (!\Input::get('news_tag')) {
                \Controller::redirect('/');
            }

            /** @var $manager NewsTagManager */
            $manager = \System::getContainer()->get('app.news_tags_manager');

            if (($tag = $manager->findByAlias(\Input::get('news_tag'))) === null) {
                \Controller::redirect('/');
            }

            if (($newsTags = NewsTagsModel::findBy('cfg_tag_id', $tag->id)) !== null)
            {
                $ids = $newsTags->fetchEach('news_id');
            }

            $this->filterColumns[] = "$t.id IN(" . implode(',', array_map('intval', $ids)) . ")";
        }
    }

    private function addCategoryFilter()
    {
        $t = static::$table;

        // Use the default filter
        if (is_array($GLOBALS['NEWS_FILTER_DEFAULT']) && !empty($GLOBALS['NEWS_FILTER_DEFAULT'])) {
            $arrCategories = \NewsCategories\NewsModel::getCategoriesCache();

            if (!empty($arrCategories)) {
                $arrIds = [];

                // Get the news IDs for particular categories
                foreach ($GLOBALS['NEWS_FILTER_DEFAULT'] as $category) {
                    if (isset($arrCategories[$category])) {
                        $arrIds = array_merge($arrCategories[$category], $arrIds);
                    }
                }

                $strKey = 'category';

                // Preserve the default category
                if ($GLOBALS['NEWS_FILTER_PRESERVE']) {
                    $strKey = 'category_default';
                }

                $strQuery = "$t.id IN (" . implode(',', (empty($arrIds) ? [0] : array_unique($arrIds))) . ")";

                if ($GLOBALS['NEWS_FILTER_PRIMARY']) {
                    $strQuery .= " AND $t.primaryCategory IN (" . implode(',', $GLOBALS['NEWS_FILTER_DEFAULT']) . ")";
                }

                $this->filterColumns[$strKey] = $strQuery;
            }
        }

        // Exclude particular news items
        if (is_array($GLOBALS['NEWS_FILTER_EXCLUDE']) && !empty($GLOBALS['NEWS_FILTER_EXCLUDE'])) {
            $this->filterColumns[] = "$t.id NOT IN (" . implode(',', array_map('intval', $GLOBALS['NEWS_FILTER_EXCLUDE'])) . ")";
        }

        $strParam = NewsCategories::getParameterName();

        // Try to find by category
        if ($GLOBALS['NEWS_FILTER_CATEGORIES'] && \Input::get($strParam)) {
            $objCategory = NewsCategoryModel::findPublishedByIdOrAlias(\Input::get($strParam));

            if ($objCategory === null) {
                return null;
            }

            $arrCategories                   = \NewsCategories\NewsModel::getCategoriesCache();
            $this->filterColumns['category'] = "$t.id IN (" . implode(',', (empty($arrCategories[$objCategory->id]) ? [0] : $arrCategories[$objCategory->id])) . ")";
        }
    }


    /**
     * Add news to list of already seen for current page
     *
     * @param integer $id News id
     * @param integer $pageId Page id
     */
    public static function addSeen($id, $pageId = null)
    {
        if ($pageId === null) {
            global $objPage;
            $pageId = $objPage->id;
        }

        $pages = \Session::getInstance()->get(static::SESSION_SEEN_NEWS);

        if (!is_array($pages)) {
            $pages = [];
        }


        $pages[$pageId][$id] = $id;

        \Session::getInstance()->set(static::SESSION_SEEN_NEWS, $pages);
    }

    /**
     * Get list of already seen news for current or given page
     *
     * @param null $pageId Set pageId or null for current page
     *
     * @return array|null List of news for current or given page id
     */
    public static function getSeen($pageId = null)
    {
        if ($pageId === null) {
            global $objPage;
            $pageId = $objPage->id;
        }

        $pages = \Session::getInstance()->get(static::SESSION_SEEN_NEWS);

        if (!is_array($pages) || !isset($pages[$pageId])) {
            return null;
        }

        return is_array($pages[$pageId]) ? $pages[$pageId] : null;
    }

    /**
     * Reset the already seen news for current page of given page
     *
     * @param null $pageId
     */
    public static function resetSeen($pageId = null)
    {
        if ($pageId === null) {
            global $objPage;
            $pageId = $objPage->id;
        }

        $pages = \Session::getInstance()->get(static::SESSION_SEEN_NEWS);

        if (is_array($pages) && isset($pages[$pageId])) {
            unset($pages[$pageId]);
            \Session::getInstance()->set(static::SESSION_SEEN_NEWS, $pages);
        }
    }
}
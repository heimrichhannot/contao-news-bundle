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


use Haste\Util\Url;
use HeimrichHannot\FieldPalette\FieldPaletteModel;
use HeimrichHannot\NewsBundle\Manager\NewsTagManager;
use HeimrichHannot\NewsBundle\Model\NewsListModel;
use HeimrichHannot\NewsBundle\Model\NewsModel;
use HeimrichHannot\NewsBundle\Model\NewsTagsModel;
use HeimrichHannot\NewsBundle\Module\ModuleNewsListFilter;
use HeimrichHannot\NewsBundle\NewsFilter\NewsFilterModule;
use HeimrichHannot\NewsBundle\QueryBuilder\NewsFilterQueryBuilder;
use NewsCategories\CategoryHelper;
use NewsCategories\NewsCategories;
use NewsCategories\NewsCategoryModel;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

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
     * The container object
     *
     * @var ContainerAwareInterface The container object
     */
    protected $container;

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
        $this->container    = \System::getContainer();
    }

    /**
     * Count news items
     *
     * @return integer|boolean Return the number of news items of false for default count behavior
     */
    public function count()
    {
        $total                         = NewsModel::countPublishedByPidsAndCallback($this->newsArchives, [$this, 'extendCount'], $this->featured, []);
        $this->module->Template->total = $total; // store total news count
        return $total;
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
        $this->addSortFilter();

        // HOOK: add custom news list count filters
        if (isset($GLOBALS['TL_HOOKS']['addNewsListCountFilters']) && is_array($GLOBALS['TL_HOOKS']['addNewsListCountFilters'])) {
            foreach ($GLOBALS['TL_HOOKS']['addNewsListCountFilters'] as $callback) {
                \System::importStatic($callback[0])->{$callback[1]}($this);
            }
        }

        /**
         * @var $filter NewsFilterModule
         */
        if (($filter = \System::getContainer()->get('huh.news.list_filter.module_registry')->get($this->module->newsListFilterModule)) !== null) {

            $builder = new NewsFilterQueryBuilder();
            $builder->setColumns($this->filterColumns);
            $builder->setValues($this->filterValues);
            $builder->setOptions($this->filterOptions);
            $filter->buildQueries($builder, true);

            $this->filterColumns = $builder->getColumns();
            $this->filterValues = $builder->getValues();
            $this->filterOptions = $builder->getOptions();
        }
    }

    /**
     * Fetch news items
     *
     * @param integer $limit Current limit from pagination
     * @param integer $offset Current offset from pagination
     *
     * @return \Contao\Model\Collection|\Contao\NewsModel[]|\Contao\NewsModel|null Return a collection it news items of false for the default fetch behavior
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
        $this->addListListeners();

        $columns = $this->filterColumns;
        $values  = $this->filterValues;
        $options = $this->filterOptions;
    }

    /**
     * Listeners that should be triggered when the list is generated
     */
    protected function addListListeners()
    {
        $this->preventDuplicateContentByPagination();
    }

    /**
     * Prevent search indexer like google from indexing pages past first page (<meta name="robots" content="noindex/follow">)
     * page and provide <link rel="prev" href="prev page url"> and <link rel="next" href="prev page url"> in head
     */
    protected function preventDuplicateContentByPagination()
    {
        if (!$this->module->Template->pagination || !$this->module->Template->total) {
            return;
        }

        // Get the current page
        $id      = 'page_n' . $this->module->id;
        $page    = (\Input::get($id) !== null) ? \Input::get($id) : 1;
        $total   = intval($this->module->Template->total);
        $perPage = $this->module->perPage;

        if ($page > 1) {
            // set all pages except first page robots to <meta name="robots" content="noindex/follow">
            $this->container->get('huh.head.tag.meta_robots')->setContent('noindex,follow');

            // prev page exists, add <link rel="prev" href="prev page url">
            $this->container->get('huh.head.tag.link_prev')->setContent(Url::addQueryString($id . '=' . ($page - 1), Url::removeQueryString([$id])));
        }

        // next page exists, add <link rel="next" href="next page url">
        if ($perPage * $page < $total - $perPage) {
            $this->container->get('huh.head.tag.link_next')->setContent(Url::addQueryString($id . '=' . ($page + 1), Url::removeQueryString([$id])));
        }
    }

    /**
     * Add additional fetch news query statement filters
     */
    protected function addFetchFilters()
    {
        $this->addNewsListFilter();
        $this->addSkipPreviousNewsFilter();
        $this->addCategoryFilter();
        $this->addTagFilter();
        $this->addSortFilter();

        // HOOK: add custom news list count filters
        if (isset($GLOBALS['TL_HOOKS']['addNewsListFetchFilters']) && is_array($GLOBALS['TL_HOOKS']['addNewsListFetchFilters'])) {
            foreach ($GLOBALS['TL_HOOKS']['addNewsListFetchFilters'] as $callback) {
                \System::importStatic($callback[0])->{$callback[1]}($this);
            }
        }

        /**
         * @var $filter NewsFilterModule
         */
        if (($filter = \System::getContainer()->get('huh.news.list_filter.module_registry')->get($this->module->newsListFilterModule)) !== null) {

            $builder = new NewsFilterQueryBuilder();
            $builder->setColumns($this->filterColumns);
            $builder->setValues($this->filterValues);
            $builder->setOptions($this->filterOptions);
            $filter->buildQueries($builder);

            $this->filterColumns = $builder->getColumns();
            $this->filterValues = $builder->getValues();
            $this->filterOptions = $builder->getOptions();
        }
    }

    /**
     * Filter out news that were already shipped by previous news list modules
     */
    private function addSkipPreviousNewsFilter()
    {
        if ($this->module->skipPreviousNews && ($skipIds = static::getSeen()) !== null) {
            $t                     = static::$table;
            $this->filterColumns[] = "$t.id NOT IN(" . implode(',', array_map('intval', $skipIds)) . ")";
        }
    }

    /**
     * Filter news list by news from tl_news_list
     */
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

    /**
     * Filter news list by tags
     */
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
            $manager = \System::getContainer()->get('huh.news.news_tags_manager');

            if (($tag = $manager->findByAlias(\Input::get('news_tag'))) === null) {
                \Controller::redirect('/');
            }

            if (($newsTags = NewsTagsModel::findBy('cfg_tag_id', $tag->id)) !== null) {
                $ids                   = $newsTags->fetchEach('news_id');
                $this->filterColumns[] = "$t.id IN(" . implode(',', array_map('intval', $ids)) . ")";
            } else {
                \Controller::redirect('/');
            }
        }
    }

    /**
     * Filter by news categories
     * @return null
     */
    private function addCategoryFilter()
    {
        $t = static::$table;

        $GLOBALS['NEWS_FILTER_CATEGORIES'] = $this->module->news_filterCategories ? true : false;
        $GLOBALS['NEWS_FILTER_DEFAULT']    = deserialize($this->module->news_filterDefault, true);
        $GLOBALS['NEWS_FILTER_PRESERVE']   = $this->module->news_filterPreserve;
        $GLOBALS['NEWS_FILTER_PRIMARY']    = $this->module->news_filterPrimaryCategory;
        $GLOBALS['NEWS_FILTER_STOP_LEVEL']  = intval($this->module->news_filterStopLevel);

        // Use the default filter
        if (is_array($GLOBALS['NEWS_FILTER_DEFAULT']) && !empty($GLOBALS['NEWS_FILTER_DEFAULT'])) {
            $arrCategories = \NewsCategories\NewsModel::getCategoriesCache();

            if (!empty($arrCategories)) {
                $arrIds = [];

                $filterCategories = $GLOBALS['NEWS_FILTER_DEFAULT'];

                if ($GLOBALS['NEWS_FILTER_STOP_LEVEL'] > 0) {
                    foreach ($GLOBALS['NEWS_FILTER_DEFAULT'] as $category) {
                        $filterCategories = array_merge($filterCategories, CategoryHelper::getCategoryIdTree($category, $GLOBALS['NEWS_FILTER_STOP_LEVEL'], true));
                    }
                }

                $filterCategories = array_unique($filterCategories);

                // Get the news IDs for particular categories
                foreach ($filterCategories as $category) {
                    if (isset($arrCategories[$category])) {
                        $arrIds = array_merge($arrCategories[$category], $arrIds);
                    }
                }

                $strKey = 'category';

                // Preserve the default category
                if ($GLOBALS['NEWS_FILTER_PRESERVE']) {
                    $strKey = 'category_default';
                }https://anwaltauskunft.de/magazin/leben/gesundheit/

                $strQuery = "$t.id IN (" . implode(',', (empty($arrIds) ? [0] : array_unique($arrIds))) . ")";

                if ($GLOBALS['NEWS_FILTER_PRIMARY']) {
                    $strQuery .= " AND $t.primaryCategory IN (" . implode(',', $filterCategories) . ")";
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

    /**
     * overwrites the order by clause by the given value
     */
    protected function addSortFilter()
    {
        if ($this->module->addCustomSort) {
            if (!empty($this->module->sortClause)) {
                $this->filterOptions['order'] = $this->module->sortClause;
            }
        }
    }


    /**
     * Add filter columns
     *
     * @return array
     */
    public function addFilterColumns(array $filterColumns)
    {
        $this->filterColumns = array_merge($this->filterColumns, $filterColumns);
    }

    /**
     * Get filter columns
     *
     * @return array
     */
    public function getFilterColumns(): array
    {
        return $this->filterColumns;
    }

    /**
     * Set filter columns
     *
     * @param array $filterColumns
     */
    public function setFilterColumns(array $filterColumns)
    {
        $this->filterColumns = $filterColumns;
    }

    /**
     * Add filter values
     *
     * @param mixed $filterValues
     */
    public function addFilterValues($filterValues)
    {
        if (!is_array($filterValues)) {
            $filterValues = [$filterValues];
        }

        $this->filterValues = array_merge(!is_array($this->filterValues) ? [] : $this->filterValues, $filterValues);
    }

    /**
     * Get filter values
     *
     * @return array|null
     */
    public function getFilterValues()
    {
        return $this->filterValues;
    }

    /**
     * Set filter values
     *
     * @param array|null $filterValues
     */
    public function setFilterValues($filterValues)
    {
        $this->filterValues = $filterValues;
    }

    /**
     * Add filter options
     *
     * @param array $filterOptions
     */
    public function addFilterOptions(array $filterOptions)
    {
        $this->filterOptions = array_merge($this->filterOptions, $filterOptions);
    }

    /**
     * Get filter options
     *
     * @return array
     */
    public function getFilterOptions(): array
    {
        return $this->filterOptions;
    }

    /**
     * Set filter options
     *
     * @param array $filterOptions
     */
    public function setFilterOptions(array $filterOptions)
    {
        $this->filterOptions = $filterOptions;
    }

    /**
     * @return array
     */
    public function getNewsArchives(): array
    {
        return $this->newsArchives;
    }

    /**
     * @return bool
     */
    public function isFeatured(): bool
    {
        return !$this->featured ? false : true;
    }

    /**
     * @return \Module
     */
    public function getModule(): \Module
    {
        return $this->module;
    }
}
<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace HeimrichHannot\NewsBundle;

use \Haste\Model\Model;
use NewsCategories\NewsCategoryModel;

/**
 * Reads and writes news
 *
 * @property integer $id
 * @property integer $pid
 * @property integer $tstamp
 * @property string  $headline
 * @property string  $alias
 * @property integer $author
 * @property integer $date
 * @property integer $time
 * @property string  $subheadline
 * @property string  $teaser
 * @property boolean $addImage
 * @property string  $singleSRC
 * @property string  $alt
 * @property string  $size
 * @property string  $imagemargin
 * @property string  $imageUrl
 * @property boolean $fullsize
 * @property string  $caption
 * @property string  $floating
 * @property boolean $addEnclosure
 * @property string  $enclosure
 * @property string  $source
 * @property integer $jumpTo
 * @property integer $articleId
 * @property string  $url
 * @property boolean $target
 * @property string  $cssClass
 * @property boolean $noComments
 * @property boolean $featured
 * @property boolean $published
 * @property string  $start
 * @property string  $stop
 *
 * @method static NewsModel|null findById($id, $opt = [])
 * @method static NewsModel|null findByPk($id, $opt = [])
 * @method static NewsModel|null findByIdOrAlias($val, $opt = [])
 * @method static NewsModel|null findOneBy($col, $val, $opt = [])
 * @method static NewsModel|null findOneByPid($val, $opt = [])
 * @method static NewsModel|null findOneByTstamp($val, $opt = [])
 * @method static NewsModel|null findOneByHeadline($val, $opt = [])
 * @method static NewsModel|null findOneByAlias($val, $opt = [])
 * @method static NewsModel|null findOneByAuthor($val, $opt = [])
 * @method static NewsModel|null findOneByDate($val, $opt = [])
 * @method static NewsModel|null findOneByTime($val, $opt = [])
 * @method static NewsModel|null findOneBySubheadline($val, $opt = [])
 * @method static NewsModel|null findOneByTeaser($val, $opt = [])
 * @method static NewsModel|null findOneByAddImage($val, $opt = [])
 * @method static NewsModel|null findOneBySingleSRC($val, $opt = [])
 * @method static NewsModel|null findOneByAlt($val, $opt = [])
 * @method static NewsModel|null findOneBySize($val, $opt = [])
 * @method static NewsModel|null findOneByImagemargin($val, $opt = [])
 * @method static NewsModel|null findOneByImageUrl($val, $opt = [])
 * @method static NewsModel|null findOneByFullsize($val, $opt = [])
 * @method static NewsModel|null findOneByCaption($val, $opt = [])
 * @method static NewsModel|null findOneByFloating($val, $opt = [])
 * @method static NewsModel|null findOneByAddEnclosure($val, $opt = [])
 * @method static NewsModel|null findOneByEnclosure($val, $opt = [])
 * @method static NewsModel|null findOneBySource($val, $opt = [])
 * @method static NewsModel|null findOneByJumpTo($val, $opt = [])
 * @method static NewsModel|null findOneByArticleId($val, $opt = [])
 * @method static NewsModel|null findOneByUrl($val, $opt = [])
 * @method static NewsModel|null findOneByTarget($val, $opt = [])
 * @method static NewsModel|null findOneByCssClass($val, $opt = [])
 * @method static NewsModel|null findOneByNoComments($val, $opt = [])
 * @method static NewsModel|null findOneByFeatured($val, $opt = [])
 * @method static NewsModel|null findOneByPublished($val, $opt = [])
 * @method static NewsModel|null findOneByStart($val, $opt = [])
 * @method static NewsModel|null findOneByStop($val, $opt = [])
 *
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByPid($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByTstamp($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByHeadline($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByAlias($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByAuthor($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByDate($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByTime($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findBySubheadline($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByTeaser($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByAddImage($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findBySingleSRC($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByAlt($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findBySize($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByImagemargin($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByImageUrl($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByFullsize($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByCaption($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByFloating($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByAddEnclosure($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByEnclosure($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findBySource($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByJumpTo($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByArticleId($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByUrl($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByTarget($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByCssClass($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByNoComments($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByFeatured($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByPublished($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByStart($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findByStop($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findMultipleByIds($val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findBy($col, $val, $opt = [])
 * @method static \Model\Collection|NewsModel[]|NewsModel|null findAll($opt = [])
 *
 * @method static integer countById($id, $opt = [])
 * @method static integer countByPid($val, $opt = [])
 * @method static integer countByTstamp($val, $opt = [])
 * @method static integer countByHeadline($val, $opt = [])
 * @method static integer countByAlias($val, $opt = [])
 * @method static integer countByAuthor($val, $opt = [])
 * @method static integer countByDate($val, $opt = [])
 * @method static integer countByTime($val, $opt = [])
 * @method static integer countBySubheadline($val, $opt = [])
 * @method static integer countByTeaser($val, $opt = [])
 * @method static integer countByAddImage($val, $opt = [])
 * @method static integer countBySingleSRC($val, $opt = [])
 * @method static integer countByAlt($val, $opt = [])
 * @method static integer countBySize($val, $opt = [])
 * @method static integer countByImagemargin($val, $opt = [])
 * @method static integer countByImageUrl($val, $opt = [])
 * @method static integer countByFullsize($val, $opt = [])
 * @method static integer countByCaption($val, $opt = [])
 * @method static integer countByFloating($val, $opt = [])
 * @method static integer countByAddEnclosure($val, $opt = [])
 * @method static integer countByEnclosure($val, $opt = [])
 * @method static integer countBySource($val, $opt = [])
 * @method static integer countByJumpTo($val, $opt = [])
 * @method static integer countByArticleId($val, $opt = [])
 * @method static integer countByUrl($val, $opt = [])
 * @method static integer countByTarget($val, $opt = [])
 * @method static integer countByCssClass($val, $opt = [])
 * @method static integer countByNoComments($val, $opt = [])
 * @method static integer countByFeatured($val, $opt = [])
 * @method static integer countByPublished($val, $opt = [])
 * @method static integer countByStart($val, $opt = [])
 * @method static integer countByStop($val, $opt = [])
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class NewsModel extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected static $strTable = 'tl_news';

    static $TYPE_NEWS = 1;

    /**
     * type
     *
     * @var \string
     */
    public $type;

    /**
     * facebookCounter
     *
     * @var \integer
     */
    public $facebook_counter;

    /**
     * facebookUpdatedAt
     *
     * @var int
     */
    public $facebook_updated_at;

    /**
     * twitterCounter
     *
     * @var \integer
     */
    public $twitter_counter;

    /**
     * twitterUpdatedAt
     *
     * @var \integer
     */
    public $twitter_updated_at;

    /**
     * googlePlusCounter
     *
     * @var \integer
     */
    public $google_plus_counter;

    /**
     * googlePlusUpdatedAt
     *
     * @var \string
     */
    public $google_plus_updated_at;

    /**
     * disqusCounter
     *
     * @var \integer
     */
    public $disqus_counter;

    /**
     * disqusUpdatedAt
     *
     * @var \integer
     */
    public $disqus_updated_at;

    /**
     * googleAnalyticCounter
     *
     * @var \integer
     */
    public $google_analytic_counter;

    /**
     * googleAnalyticUpdatedAt
     *
     * @var \integer
     */
    public $google_analytic_updated_at;

    /**
     * @return string
     */
    public static function getStrTable(): string
    {
        return self::$strTable;
    }

    /**
     * @param string $strTable
     */
    public static function setStrTable(string $strTable)
    {
        self::$strTable = $strTable;
    }

    protected static function filterBySources($arrColumns, $news_source)
    {
        $t = static::$strTable;

        $objTags = NewsTagsModel::findAll();
        if ($objTags !== null)
        {
            $arrNewsIds = [];
            foreach ($objTags as $entry)
            {
                $arrNewsIds[] = $entry->news_id;
            }
            $arrColumns[] = "$t.id IN (" . implode(',', (empty($arrNewsIds) ? [] : array_unique($arrNewsIds))) . ")";
        }

        return $arrColumns;
    }

    /**
     * finds news item by its ids and an additional condition
     *
     * @param string $column     the column of the additional field
     * @param string $val        the value of the additional field
     * @param array  $arrIds     an array of ids
     * @param array  $arrOptions options
     *
     * @return \Contao\Model\Collection|\Contao\NewsModel|\Contao\NewsModel[]|null
     */
    public static function findByAndInIds($column, $val, $arrIds, array $arrOptions = [])
    {
        if (!is_array($arrIds) || empty($arrIds))
        {
            return null;
        }
        $t            = static::$strTable;
        $arrColumns[] = "$t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")";
        $arrColumns[] = "$t.$column = $val";

        return static::findBy($arrColumns, null, $arrOptions);
    }

    /**
     * Find a published news item from one or more news archives by its ID or alias
     *
     * @param mixed $varId      The numeric ID or alias name
     * @param array $arrPids    An array of parent IDs
     * @param array $arrOptions An optional options array
     *
     * @return NewsModel|null The model or null if there are no news
     */
    public static function findPublishedByParentAndIdOrAlias($varId, $arrPids, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t            = static::$strTable;
        $arrColumns   = !is_numeric($varId) ? ["$t.alias=?"] : ["$t.id=?"];
        $arrColumns[] = "$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")";

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN)
        {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        return static::findOneBy($arrColumns, $varId, $arrOptions);
    }


    /**
     * Find published news items by their parent ID
     *
     * @param array   $arrPids     An array of news archive IDs
     * @param boolean $blnFeatured If true, return only featured news, if false, return only unfeatured news
     * @param integer $intLimit    An optional limit
     * @param integer $intOffset   An optional offset
     * @param array   $arrOptions  An optional options array
     *
     * @return \\Model\Collection|NewsModel[]|NewsModel|null A collection of models or null if there are no news
     */
    public static function findPublishedByPids($arrPids, $blnFeatured = null, $intLimit = 0, $intOffset = 0, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t          = static::$strTable;
        $arrColumns = ["$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if ($blnFeatured === true)
        {
            $arrColumns[] = "$t.featured='1'";
        }
        elseif ($blnFeatured === false)
        {
            $arrColumns[] = "$t.featured=''";
        }

        $arrColumns = static::filterBySources($arrColumns, $arrOptions['news_source']);
    }

    /**
     * @param string     $strSource News source
     * @param int|string $varId     Id or unique alias of news channel. 0 for all channels of type
     * @param int        $intLimit
     * @param int        $intOffset
     * @param array      $arrOptions
     *
     * @return NewsModel|NewsModel[]|\Model\Collection|null
     */
    public static function findPublishedByNewsSource($strSource, $varId = 0, $intLimit = 0, $intOffset = 0, $arrOptions)
    {
        if (!is_string($strSource) && empty($strSource))
        {
            return null;
        }
        if (!is_int($varId) && !is_string($varId))
        {
            return null;
        }
        $t         = static::$strTable;
        $objSource = \System::getContainer()->get('hh.news-bundle.news_feed_generator')->getFeedSource($strSource);
        if ($varId !== 0)
        {
            $objChannel = $objSource->getChannel($varId);
            $objNews    = $objSource->getItemsByChannel($objChannel);
            if ($objNews === null)
            {
                return null;
            }
            $arrNewsIds = [];
            while ($objNews->next())
            {
                $arrNewsIds[] = $objNews->id;
            }
        }
        else
        {
            $objChannels = $objSource->getChannels();
            $arrNewsIds  = [];
            while ($objChannels->next())
            {
                $objNews = $objSource->getItemsByChannel($objChannels);
                if ($objNews === null)
                {
                    continue;
                }
                while ($objNews->next())
                {
                    $arrNewsIds[] = $objNews->id;
                }
            }
        }
        $arrColumns[] = "$t.id IN (" . implode(',', (empty($arrNewsIds) ? [] : array_unique($arrNewsIds))) . ")";

        return static::findPublished($arrColumns, $intLimit, $intOffset, $arrOptions);
    }

    private static function findPublished($arrColumns, $intLimit = 0, $intOffset = 0, array $arrOptions = [])
    {
        $t = static::$strTable;
        // Never return unpublished elements in the back end, so they don't end up in the RSS feed
        if (!BE_USER_LOGGED_IN || TL_MODE == 'BE')
        {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.date DESC";
        }

        $arrOptions['limit']  = $intLimit;
        $arrOptions['offset'] = $intOffset;

        return static::findBy($arrColumns, null, $arrOptions);
    }


    /**
     * Count published news items by their parent ID
     *
     * @param array   $arrPids     An array of news archive IDs
     * @param boolean $blnFeatured If true, return only featured news, if false, return only unfeatured news
     * @param array   $arrOptions  An optional options array
     *
     * @return integer The number of news items
     */
    public static function countPublishedByPids($arrPids, $blnFeatured = null, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return 0;
        }

        $t          = static::$strTable;
        $arrColumns = ["$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if ($blnFeatured === true)
        {
            $arrColumns[] = "$t.featured='1'";
        }
        elseif ($blnFeatured === false)
        {
            $arrColumns[] = "$t.featured=''";
        }

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN)
        {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        return static::countBy($arrColumns, null, $arrOptions);
    }


    /**
     * Find published news items with the default redirect target by their parent ID
     *
     * @param integer $intPid     The news archive ID
     * @param array   $arrOptions An optional options array
     *
     * @return \Model\Collection|NewsModel[]|NewsModel|null A collection of models or null if there are no news
     */
    public static function findPublishedDefaultByPid($intPid, array $arrOptions = [])
    {
        $t          = static::$strTable;
        $arrColumns = ["$t.pid=? AND $t.source='default'"];

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN)
        {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.date DESC";
        }

        return static::findBy($arrColumns, $intPid, $arrOptions);
    }


    /**
     * Find published news items by their parent ID
     *
     * @param integer $intId      The news archive ID
     * @param integer $intLimit   An optional limit
     * @param array   $arrOptions An optional options array
     *
     * @return \Model\Collection|NewsModel[]|NewsModel|null A collection of models or null if there are no news
     */
    public static function findPublishedByPid($intId, $intLimit = 0, array $arrOptions = [])
    {
        $t          = static::$strTable;
        $arrColumns = ["$t.pid=?"];

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN)
        {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.date DESC";
        }

        if ($intLimit > 0)
        {
            $arrOptions['limit'] = $intLimit;
        }

        return static::findBy($arrColumns, $intId, $arrOptions);
    }


    /**
     * Find all published news items of a certain period of time by their parent ID
     *
     * @param integer $intFrom    The start date as Unix timestamp
     * @param integer $intTo      The end date as Unix timestamp
     * @param array   $arrPids    An array of news archive IDs
     * @param integer $intLimit   An optional limit
     * @param integer $intOffset  An optional offset
     * @param array   $arrOptions An optional options array
     *
     * @return \Model\Collection|NewsModel[]|NewsModel|null A collection of models or null if there are no news
     */
    public static function findPublishedFromToByPids($intFrom, $intTo, $arrPids, $intLimit = 0, $intOffset = 0, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t          = static::$strTable;
        $arrColumns = ["$t.date>=? AND $t.date<=? AND $t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN)
        {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.date DESC";
        }

        $arrOptions['limit']  = $intLimit;
        $arrOptions['offset'] = $intOffset;

        return static::findBy($arrColumns, [$intFrom, $intTo], $arrOptions);
    }


    /**
     * Count all published news items of a certain period of time by their parent ID
     *
     * @param integer $intFrom    The start date as Unix timestamp
     * @param integer $intTo      The end date as Unix timestamp
     * @param array   $arrPids    An array of news archive IDs
     * @param array   $arrOptions An optional options array
     *
     * @return integer The number of news items
     */
    public static function countPublishedFromToByPids($intFrom, $intTo, $arrPids, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids))
        {
            return null;
        }

        $t          = static::$strTable;
        $arrColumns = ["$t.date>=? AND $t.date<=? AND $t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN)
        {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        return static::countBy($arrColumns, [$intFrom, $intTo], $arrOptions);
    }

    public function getUrl($baseUrl)
    {
        $urlString[] = \NewsArchiveModel::findByPk($this->pid) == null ? null : \NewsArchiveModel::findByPk($this->pid)->title;
        $urlString[] = NewsCategoryModel::findPublishedByIdOrAlias($this->id) == null ? null : NewsCategoryModel::findPublishedByIdOrAlias($this->id)->title;
        $urlString[] = $this->alias . '.html';
        $url         = $baseUrl;
        foreach ($urlString as $string)
        {
            if ($string !== null)
            {
                $url .= '/' . strtolower($string);
            }
        }

        return urlencode($url);
    }

    /**
     * @param bool $countOnly
     * @param bool $offset
     * @param bool $limit
     *
     * @return NewsModel|NewsModel[]|int|\Model\Collection|null
     */
    public static function getAllForSocialStatsUpdate($countOnly = false, $offset = false, $limit = false)
    {
        $arrOptions['order'] = 'date ASC';
        $tsPeriod            = time() - (60 * 60 * 24 * 180); // 180 days

        $column = ['date>?', 'hidden=?'];
        $value  = [$tsPeriod, 0];

        if (false !== $offset)
        {
            $arrOptions['offset'] = $offset;
        }
        if (false !== $limit)
        {
            $arrOptions['limit'] = $limit;
        }

        if ($countOnly)
        {
            $result = NewsModel::findBy($column, $value, $arrOptions)->count();
        }
        else
        {
            $result = NewsModel::findBy($column, $value, $arrOptions);
        }

        return $result;
    }

    /**
     * @param int $pid
     *
     * @return array|\Contao\Database\Result|object
     */
    public static function getNewsYearsByPid($pId)
    {
        $t     = static::$strTable;
        $years = \Database::getInstance()->prepare("SELECT FROM_UNIXTIME(date, '%Y') as year FROM $t WHERE pid = $pId GROUP BY year")->execute();

        if (!$years->numRows)
        {
            return [];
        }

        return $years;
    }

    /**
     * @param int $pid
     *
     * @return array|\Contao\Database\Result|object
     */
    public static function getNewsMonthsByYearAndPid($pId, $year)
    {
        $yearStart = mktime(0, 0, 0, 0, 0, intval($year));
        $yearEnd   = mktime(0, 0, 0, 0, 0, intval($year) + 1);

        $t      = static::$strTable;
        $months = \Database::getInstance()
            ->prepare("SELECT FROM_UNIXTIME(date, '%m') as month FROM $t WHERE pid = $pId AND date > $yearStart AND date < $yearEnd GROUP BY month")
            ->execute();

        if (!$months->numRows)
        {
            return [];
        }

        return $months;
    }

    /**
     * @param       $year
     * @param array $arrOptions
     *
     * @return NewsModel|NewsModel[]|\Model\Collection|null
     */
    public static function getNewsByYearAndPid($year, $pId, array $arrOptions = [])
    {
        $yearStart = mktime(0, 0, 0, 0, 0, intval($year));
        $yearEnd   = mktime(0, 0, 0, 0, 0, intval($year) + 1);

        return self::findBy(['date > ?', 'date < ?', 'pid=?'], [$yearStart, $yearEnd, $pId], $arrOptions);
    }

    /**
     * @param       $year
     * @param array $arrOptions
     *
     * @return NewsModel|NewsModel[]|\Model\Collection|null
     */
    public static function getNewsByYearMonthAndPid($year, $month, $pId, array $arrOptions = [])
    {
        $yearStart = mktime(0, 0, 0, intval($month), 1, intval($year));
        $yearEnd   = mktime(0, 0, 0, intval($month) + 1, 1, intval($year));

        return self::findBy(['date > ?', 'date < ?', 'pid=?'], [$yearStart, $yearEnd, $pId], $arrOptions);
    }
}

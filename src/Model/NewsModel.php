<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace HeimrichHannot\NewsBundle\Model;

class NewsModel extends \Contao\NewsModel
{
    /**
     * Count published news by pid and additional criterias
     * @param array $pid
     * @param array $columns
     * @param array|null $values
     * @param array $options
     * @return integer The number of matching rows
     */
    public static function countPublishedByPidAndCriteria(array $pid, array $columns = [], $values = null, $options = [])
    {
        $t         = static::$strTable;
        $columns[] = "$t.pid IN(" . implode(',', array_map('intval', $pid)) . ")";

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN) {
            $time      = \Date::floorToMinute();
            $columns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        return static::countBy($columns, $values, $options);
    }

    /**
     * Find published news by pid and additional criterias
     * @param array $pid
     * @param array $columns
     * @param array|null $values
     * @param array $options
     * @return \Contao\Model\Collection|\Contao\NewsModel[]|\Contao\NewsModel|null A collection of models or null if there are no news
     */
    public static function findPublishedByPidAndCriteria(array $pid, array $columns = [], $values = null, $options = [])
    {
        $t         = static::$strTable;
        $columns[] = "$t.pid IN(" . implode(',', array_map('intval', $pid)) . ")";

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN) {
            $time      = \Date::floorToMinute();
            $columns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        return static::findBy($columns, $values, $options);
    }

    /**
     * Find news items by oldest Facebook counter update date
     *
     * @param int $limit
     * @param int $days
     * @param array $pids
     * @param array $options
     *
     * @return \Contao\Model\Collection|\Contao\NewsModel[]|\Contao\NewsModel|null A collection of models or null if there are no news
     */
    public static function findByFacebookCounterUpdateDate($limit = 20, $days = 180, $pids = [], $options = [])
    {
        $t                = static::$strTable;
        $options['order'] = "$t.facebook_updated_at ASC";

        return static::findForSocialStats($limit, $days, $pids, $options);
    }

    /**
     * Find news items by oldest Twitter counter update date
     *
     * @param int $limit
     * @param int $days
     * @param array $pids
     * @param array $options
     *
     * @return \Contao\Model\Collection|\Contao\NewsModel[]|\Contao\NewsModel|null A collection of models or null if there are no news
     */
    public static function findByTwitterCounterUpdateDate($limit = 20, $days = 180, $pids = [], $options = [])
    {
        $t                = static::$strTable;
        $options['order'] = "$t.twitter_updated_at ASC";

        return static::findForSocialStats($limit, $days, $pids, $options);
    }

    /**
     * Find news items by oldest Google Plus update date
     *
     * @param int $limit
     * @param int $days
     * @param array $pids
     * @param array $options
     *
     * @return \Contao\Model\Collection|\Contao\NewsModel[]|\Contao\NewsModel|null A collection of models or null if there are no news
     */
    public static function findByGooglePlusCounterUpdateDate($limit = 20, $days = 180, $pids = [], $options = [])
    {
        $t                = static::$strTable;
        $options['order'] = "$t.google_plus_updated_at ASC";

        return static::findForSocialStats($limit, $days, $pids, $options);
    }

    /**
     * Find news items by oldest Disqus update date
     *
     * @param int $limit
     * @param int $days
     * @param array $pids
     * @param array $options
     *
     * @return \Contao\Model\Collection|\Contao\NewsModel[]|\Contao\NewsModel|null A collection of models or null if there are no news
     */
    public static function findByDisqusCounterUpdateDate($limit = 20, $days = 180, $pids = [], $options = [])
    {
        $t                = static::$strTable;
        $options['order'] = "$t.disqus_updated_at ASC";

        return static::findForSocialStats($limit, $days, $pids, $options);
    }

    /**
     * Find news items by oldest Google Analytics update da
     *
     * @param int $limit
     * @param int $days
     * @param array $pids
     * @param array $options
     *
     * @return \Contao\Model\Collection|\Contao\NewsModel[]|\Contao\NewsModel|null A collection of models or null if there are no news
     */
    public static function findByGoogleAnalyticsUpdateDate($limit = 20, $days = 180, $pids = [], $options = [])
    {
        $t                = static::$strTable;
        $options['order'] = "$t.google_analytic_updated_at ASC";

        return static::findForSocialStats($limit, $days, $pids, $options);
    }

    /**
     * Find news items for social stats
     *
     * @param int $limit
     * @param int $days
     * @param array $pids
     * @param array $options
     *
     * @return \Contao\Model\Collection|\Contao\NewsModel[]|\Contao\NewsModel|null A collection of models or null if there are no news
     */
    public static function findForSocialStats($limit = 20, $days = 180, $pids = [], $options = [])
    {
        $t          = static::$strTable;
        $arrColumns = ["$t.published = 1"];
        if ($options['order']) {
            $options['order'] = $options['order'] . ", $t.date DESC";
        } else {
            $options['order'] = "$t.date DESC";
        }

        if ($days > 0) {
            $period       = time() - (60 * 60 * 24 * $days);
            $arrColumns[] = "$t.date > $period";
        }
        if ($limit > 0) {
            $options['limit'] = $limit;
        }

        if (!empty($pids)) {
            $arrColumns[] = "$t.pid IN(" . implode(',', array_map('intval', $pids)) . ")";
        }

        return static::findBy($arrColumns, null, $options);
    }

    /**
     * Return an array containing all years containing published news
     *
     * @param array $pid The parent news archives
     *
     * @return array A list of years
     */
    public static function getPublishedYearsByPids(array $pid)
    {
        $t = static::$strTable;

        $query = "SELECT FROM_UNIXTIME(date, '%Y') as year FROM $t WHERE $t.pid IN(" . implode(',', array_map('intval', $pid)) . ")";

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN) {
            $time  = \Date::floorToMinute();
            $query .= " AND ($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        $query .= " GROUP BY year ORDER BY year DESC";

        $years = \Database::getInstance()->prepare($query)->execute();

        if (!$years->numRows) {
            return [];
        }

        return array_combine($years->fetchEach('year'), $years->fetchEach('year'));
    }

    /**
     * Return an array containing all month containing published news within a given year
     *
     * @param array $pid The parent news archives
     *
     * @return array A list of month within the given year
     */
    public static function getPublishedMonthsByYearAndPids(array $pid, $year)
    {
        $yearStart = mktime(0, 0, 0, 0, 0, intval($year));
        $yearEnd   = mktime(0, 0, 0, 0, 0, intval($year) + 1);

        $t = static::$strTable;

        $query = "SELECT FROM_UNIXTIME(date, '%m') as month FROM $t WHERE pid IN(" . implode(',', array_map('intval', $pid)) . ") AND date > ? AND date < ?";

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN) {
            $time  = \Date::floorToMinute();
            $query .= " AND ($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        $query .= " GROUP BY month ORDER BY month DESC";

        $months = \Database::getInstance()->prepare($query)->execute($yearStart, $yearEnd);

        if (!$months->numRows) {
            return [];
        }

        return $months->fetchEach('month');
    }

    /**
     * Get published news items within a given year and pids
     *
     * @param int $year The year value (for example 2017)
     * @param       array pids The parent news archives
     * @param array $arrOptions
     *
     * @return \Contao\Model\Collection|\Contao\NewsModel[]|\Contao\NewsModel|null A collection of models or null if there are no news
     */
    public static function findPublishedByYearAndPids($year, array $pid, array $arrOptions = [])
    {
        $yearStart = mktime(0, 0, 0, 0, 0, intval($year));
        $yearEnd   = mktime(0, 0, 0, 0, 0, intval($year) + 1);

        $t = static::$strTable;

        $arrColumns = ["$t.date > ? AND $t.date < ? AND $t.pid IN(" . implode(',', array_map('intval', $pid)) . ")"];

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN) {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        return static::findBy($arrColumns, [$yearStart, $yearEnd], $arrOptions);
    }

    /**
     * Get published news items within a given year and month and pids
     *
     * @param  int $month The month to search in given year
     * @param  int $year The year to search in
     * @param array $arrOptions
     *
     * @return \Contao\NewsModel|\Contao\NewsModel[]|\Model\Collection|null
     */
    public static function findPublishedByYearMonthAndPids($year, $month, array $pid, array $arrOptions = [])
    {
        $yearStart = mktime(0, 0, 0, intval($month), 1, intval($year));
        $yearEnd   = mktime(0, 0, 0, intval($month) + 1, 1, intval($year));

        $t = static::$strTable;

        $arrColumns = ["$t.date > ? AND $t.date < ? AND $t.pid IN(" . implode(',', array_map('intval', $pid)) . ")"];

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN) {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        return static::findBy($arrColumns, [$yearStart, $yearEnd], $arrOptions);
    }

    /**
     * Count published news items by their parent ID
     *
     * @param array $arrPids An array of news archive IDs
     * @param array $arrIds An array of news IDs
     * @param boolean $blnFeatured If true, return only featured news, if false, return only unfeatured news
     * @param array $arrOptions An optional options array
     *
     * @return integer The number of news items
     */
    public static function countPublishedByPidsAndIds($arrPids, $arrIds, $blnFeatured = null, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids)) {
            return 0;
        }

        $t          = static::$strTable;
        $arrColumns = ["$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if (is_array($arrIds) && !empty($arrIds)) {
            $arrColumn[] = "$t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")";
        }

        if ($blnFeatured === true) {
            $arrColumns[] = "$t.featured='1'";
        } elseif ($blnFeatured === false) {
            $arrColumns[] = "$t.featured=''";
        }

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN) {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        return static::countBy($arrColumns, null, $arrOptions);
    }

    /**
     * Find published news items by their parent ID
     *
     * @param array $arrPids An array of news archive IDs
     * @param array $arrIds An array of news IDs
     * @param boolean $blnFeatured If true, return only featured news, if false, return only unfeatured news
     * @param integer $intLimit An optional limit
     * @param integer $intOffset An optional offset
     * @param array $arrOptions An optional options array
     *
     * @return \Contao\Model\Collection|\Contao\NewsModel[]|\Contao\NewsModel|null A collection of models or null if there are no news
     */
    public static function findPublishedByPidsAndIds($arrPids, $arrIds, $blnFeatured = null, $intLimit = 0, $intOffset = 0, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids)) {
            return null;
        }

        $t          = static::$strTable;
        $arrColumns = ["$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if (is_array($arrIds) && !empty($arrIds)) {
            $arrColumns[] = "$t.id IN(" . implode(',', array_map('intval', $arrIds)) . ")";
        }

        if ($blnFeatured === true) {
            $arrColumns[] = "$t.featured='1'";
        } elseif ($blnFeatured === false) {
            $arrColumns[] = "$t.featured=''";
        }

        // Never return unpublished elements in the back end, so they don't end up in the RSS feed
        if (!BE_USER_LOGGED_IN || TL_MODE == 'BE') {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.date DESC";
        }

        $arrOptions['limit']  = $intLimit;
        $arrOptions['offset'] = $intOffset;

        return static::findBy($arrColumns, null, $arrOptions);
    }

    /**
     * Count published news items by their parent ID
     *
     * @param array $arrPids An array of news archive IDs
     * @param array $callback A callback function to modify $arrColumns, $arrValues and $arrOptions
     * @param boolean $blnFeatured If true, return only featured news, if false, return only unfeatured news
     * @param array $arrOptions An optional options array
     *
     * @return integer The number of news items
     */
    public static function countPublishedByPidsAndCallback($arrPids, $callback = null, $blnFeatured = null, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids)) {
            return 0;
        }

        $t          = static::$strTable;
        $arrValues  = null;
        $arrColumns = ["$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if ($blnFeatured === true) {
            $arrColumns[] = "$t.featured='1'";
        } elseif ($blnFeatured === false) {
            $arrColumns[] = "$t.featured=''";
        }

        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN) {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        if ($callback !== null) {
            if (is_array($callback)) {
                if (is_callable($callback)) {
                    $callback($arrColumns, $arrValues, $arrOptions);
                } else {
                    \Controller::importStatic($callback[0])->{$callback[1]}($arrColumns, $arrValues, $arrOptions);
                }
            }
        }

        return static::countBy($arrColumns, $arrValues, $arrOptions);
    }

    /**
     * Find published news items by their parent ID
     *
     * @param array $arrPids An array of news archive IDs
     * @param array $callback A callback function to modify $arrColumns, $arrValues and $arrOptions
     * @param boolean $blnFeatured If true, return only featured news, if false, return only unfeatured news
     * @param integer $intLimit An optional limit
     * @param integer $intOffset An optional offset
     * @param array $arrOptions An optional options array
     *
     * @return \Contao\Model\Collection|\Contao\NewsModel[]|\Contao\NewsModel|null A collection of models or null if there are no news
     */
    public static function findPublishedByPidsAndCallback($arrPids, $callback = null, $blnFeatured = null, $intLimit = 0, $intOffset = 0, array $arrOptions = [])
    {
        if (!is_array($arrPids) || empty($arrPids)) {
            return null;
        }

        $t          = static::$strTable;
        $arrValues  = null;
        $arrColumns = ["$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"];

        if ($blnFeatured === true) {
            $arrColumns[] = "$t.featured='1'";
        } elseif ($blnFeatured === false) {
            $arrColumns[] = "$t.featured=''";
        }

        // Never return unpublished elements in the back end, so they don't end up in the RSS feed
        if (!BE_USER_LOGGED_IN || TL_MODE == 'BE') {
            $time         = \Date::floorToMinute();
            $arrColumns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        if (!isset($arrOptions['order'])) {
            $arrOptions['order'] = "$t.date DESC";
        }

        $arrOptions['limit']  = $intLimit;
        $arrOptions['offset'] = $intOffset;

        if ($callback !== null) {
            if (is_array($callback)) {
                if (is_callable($callback)) {
                    $callback($arrColumns, $arrValues, $arrOptions);
                } else {
                    \Controller::importStatic($callback[0])->{$callback[1]}($arrColumns, $arrValues, $arrOptions);
                }
            }
        }

        return static::findBy($arrColumns, $arrValues, $arrOptions);
    }

    public static function findMultipleByIdsAndPids($arrIds, $arrPids, array $arrOptions = [])
    {
        if (empty($arrIds) || !is_array($arrIds)) {
            return null;
        }

        $arrRegistered   = [];
        $arrUnregistered = [];

        // Search for registered models
        foreach ($arrIds as $intId) {
            if (empty($arrOptions)) {
                $arrRegistered[$intId] = \Model\Registry::getInstance()->fetch(static::$strTable, $intId);
            }

            if (!isset($arrRegistered[$intId])) {
                $arrUnregistered[] = $intId;
            }
        }

        // Fetch only the missing models from the database
        if (!empty($arrUnregistered)) {
            $t = static::$strTable;

            $arrOptions = array_merge
            (
                [
                    'column' => [
                        "$t.id IN(" . implode(',', array_map('intval', $arrUnregistered)) . ")",
                        "$t.pid IN(" . implode(',', array_map('intval', $arrPids)) . ")"
                    ],
                    'value'  => null,
                    'order'  => \Database::getInstance()->findInSet("$t.id", $arrIds),
                    'return' => 'Collection'
                ],

                $arrOptions
            );

            $objMissing = static::find($arrOptions);

            if ($objMissing !== null) {
                while ($objMissing->next()) {
                    $intId                 = $objMissing->{static::$strPk};
                    $arrRegistered[$intId] = $objMissing->current();
                }
            }
        }

        return static::createCollection(array_filter(array_values($arrRegistered)), static::$strTable);
    }

    /**
     * Return the next (newer) article
     *
     * @param $time
     * @param array $columns
     * @param array $options
     * @return \Contao\NewsModel|null
     */
    public static function findNextPublishedByReleaseDate($time, $columns = [], $options = [])
    {
        $t         = static::$strTable;
        $values    = [];
        $columns[] = "$t.time > ?";
        $values[]  = $time;
        if (empty($options['order']))
        {
            $options['order'] = "$t.time ASC";
        } else
        {
            $options['order'] .= ", $t.time ASC";
        }
        return static::findOnePublished($columns, $values, $options);
    }

    /**
     * Return the previous (older) article
     *
     * @param $time
     * @param array $columns
     * @param array $options
     * @return \Contao\NewsModel|null
     */
    public static function findPreviousPublishedByReleaseDate($time, $columns = [], $options = [])
    {
        $t         = static::$strTable;
        $values    = [];
        $columns[] = "$t.time < ?";
        $values[]  = $time;
        if (empty($options['order']))
        {
            $options['order'] = "$t.time DESC";
        } else
        {
            $options['order'] .= ", $t.time DESC";
        }
        return static::findOnePublished($columns, $values, $options);
    }

    /**
     * Return a single published article
     *
     * @param array $columns
     * @param array $values
     * @param array $options
     * @return \Contao\NewsModel|null
     */
    public static function findOnePublished($columns = [], $values = [], $options = [])
    {
        $t = static::$strTable;
        if (isset($arrOptions['ignoreFePreview']) || !BE_USER_LOGGED_IN)
        {
            $time      = \Date::floorToMinute();
            $columns[] = "($t.start='' OR $t.start<='$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }
        return static::findOneBy($columns, $values, $options);
    }
}

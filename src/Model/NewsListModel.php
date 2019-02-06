<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Model;

use Contao\Config;
use Contao\Environment;
use Contao\Model;
use Contao\PageModel;
use HeimrichHannot\Haste\Dca\General;

class NewsListModel extends Model
{
    protected static $strTable = 'tl_news_list';

    /**
     * URL cache array.
     *
     * @var array
     */
    private static $urlCache = [];

    /**
     * @param $newsList NewsListModel|int The news list as object or id
     *
     * @return string
     */
    public static function generateNewsListUrl($newsList): string
    {
        $newsList = General::getModelInstanceIfId($newsList, 'tl_news_list');

        if (null === $newsList || null === ($newsListArchive = $newsList->getRelated('pid'))) {
            return null;
        }

        $cacheKey = 'id_'.$newsList->id;

        // Load the URL from cache
        if (isset(self::$urlCache[$cacheKey])) {
            return self::$urlCache[$cacheKey];
        }

        $page = PageModel::findByPk($newsListArchive->jumpTo);

        if (!$page instanceof \PageModel) {
            self::$urlCache[$cacheKey] = ampersand(Environment::get('request'), true);
        } else {
            self::$urlCache[$cacheKey] = ampersand($page->getFrontendUrl((Config::get('useAutoItem') ? '/' : '/items/').($newsList->alias ?: $newsList->id)));
        }

        return self::$urlCache[$cacheKey];
    }
}

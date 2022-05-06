<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\StringUtil;
use HeimrichHannot\NewsBundle\Model\NewsListArchiveModel;
use HeimrichHannot\NewsBundle\Model\NewsListModel;
use HeimrichHannot\UtilsBundle\Model\CfgTagModel;
use Model\Collection;

class SearchablePagesListener
{
    /**
     * Add news items to the indexer.
     *
     * @param array $pages
     * @param int   $rootId
     * @param bool  $isSitemap
     *
     * @return array
     */
    public function getSearchableNewsListPages($pages, $rootId = 0, $isSitemap = false)
    {
        $root = [];

        if ($rootId > 0) {
            $root = \Database::getInstance()->getChildRecords($rootId, 'tl_page');
        }

        $processed = [];
        $time = \Date::floorToMinute();

        if (null !== ($archive = NewsListArchiveModel::findAll())) {
            while ($archive->next()) {
                if (!$archive->jumpTo || !empty($root) && !\in_array($archive->jumpTo, $root, true)) {
                    continue;
                }

                if (!isset($processed[$archive->jumpTo])) {
                    if (null === ($parent = \PageModel::findWithDetails($archive->jumpTo))) {
                        continue;
                    }

                    if (!$parent->published || ('' != $parent->start && $parent->start > $time) || ('' != $parent->stop && $parent->stop <= ($time + 60))) {
                        continue;
                    }

                    if ($isSitemap) {
                        if ($parent->protected) {
                            continue;
                        }

                        if ('map_never' == $parent->sitemap) {
                            continue;
                        }
                    }

                    // Generate the URL
                    $processed[$archive->jumpTo] = $parent->getAbsoluteUrl(\Config::get('useAutoItem') ? '/%s' : '/items/%s');
                }

                $url = $processed[$archive->jumpTo];

                if (null !== ($newsList = NewsListModel::findBy(['pid=?', 'published=?'], [$archive->id, true]))) {
                    while ($newsList->next()) {
                        $pages[] = sprintf($url, $newsList->alias ?: $newsList->id);
                    }
                }
            }
        }

        return $pages;
    }
}

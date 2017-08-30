<?php

namespace HeimrichHannot\NewsBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\StringUtil;
use HeimrichHannot\NewsBundle\Manager\NewsTagManager;
use HeimrichHannot\NewsBundle\Model\CfgTagModel;
use HeimrichHannot\NewsBundle\Model\NewsListArchiveModel;
use HeimrichHannot\NewsBundle\Model\NewsListModel;
use HeimrichHannot\NewsBundle\Model\NewsTagsModel;
use Model\Collection;

class SearchablePagesListener
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * Constructor.
     *
     * @param ContaoFrameworkInterface $framework
     */
    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    public function getSearchablePages($pages, $rootId = 0, $isSitemap = false)
    {
        $pages = $this->getSearchableNewsListPages($pages, $rootId, $isSitemap);
        $pages = $this->getSearchableNewsTagPages($pages, $rootId, $isSitemap);

        return $pages;
    }

    /**
     * Add news items to the indexer
     *
     * @param array $pages
     * @param integer $rootId
     * @param boolean $isSitemap
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
        $time      = \Date::floorToMinute();

        if (($archive = NewsListArchiveModel::findAll()) !== null) {
            while ($archive->next()) {
                if (!$archive->jumpTo || !empty($root) && !in_array($archive->jumpTo, $root)) {
                    continue;
                }

                if (!isset($processed[$archive->jumpTo])) {
                    if (($parent = \PageModel::findWithDetails($archive->jumpTo)) === null) {
                        continue;
                    }

                    if (!$parent->published || ($parent->start != '' && $parent->start > $time) || ($parent->stop != '' && $parent->stop <= ($time + 60))) {
                        continue;
                    }

                    if ($isSitemap) {
                        if ($parent->protected) {
                            continue;
                        }

                        if ($parent->sitemap == 'map_never') {
                            continue;
                        }
                    }

                    // Generate the URL
                    $processed[$archive->jumpTo] = $parent->getAbsoluteUrl(\Config::get('useAutoItem') ? '/%s' : '/items/%s');
                }

                $url = $processed[$archive->jumpTo];

                if (($newsList = NewsListModel::findBy(['pid=?', 'published=?'], [$archive->id, true])) !== null) {
                    while ($newsList->next()) {
                        $pages[] = sprintf($url, $newsList->alias ?: $newsList->id);
                    }
                }
            }
        }

        return $pages;
    }

    /**
     * Add news items to the indexer
     *
     * @param array $pages
     * @param integer $rootId
     * @param boolean $isSitemap
     *
     * @return array
     */
    public function getSearchableNewsTagPages($pages, $rootId = 0, $isSitemap = false)
    {
        $root = [];

        if ($rootId > 0) {
            $root = \Database::getInstance()->getChildRecords($rootId, 'tl_page');
        }

        $processed = [];
        $time      = \Date::floorToMinute();

        foreach (StringUtil::deserialize(\Config::get('tagSourceJumpTos'), true) as $tagSource) {
            $jumpTo = $tagSource['jumpTo'];
            $source = $tagSource['source'];

            if (!$jumpTo || !empty($root) && !in_array($jumpTo, $root)) {
                continue;
            }

            if (!isset($processed[$jumpTo])) {
                if (($parent = \PageModel::findWithDetails($jumpTo)) === null) {
                    continue;
                }

                if (!$parent->published || ($parent->start != '' && $parent->start > $time) || ($parent->stop != '' && $parent->stop <= ($time + 60))) {
                    continue;
                }

                if ($isSitemap) {
                    if ($parent->protected) {
                        continue;
                    }

                    if ($parent->sitemap == 'map_never') {
                        continue;
                    }
                }

                // Generate the URL
                $processed[$jumpTo] = $parent->getAbsoluteUrl(\Config::get('useAutoItem') ? '/%s' : '/items/%s');
            }

            $url = $processed[$jumpTo];

            /** @var Collection $tags */
            if (($tags = CfgTagModel::findAllBySource($source)) !== null) {
                foreach (array_combine($tags->fetchEach('id'), $tags->fetchEach('alias')) as $id => $tag)
                {
                    $pages[] = sprintf($url, $tag ?: $id);
                }
            }
        }

        return $pages;
    }

}
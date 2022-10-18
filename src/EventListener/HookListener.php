<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use HeimrichHannot\NewsBundle\NewsArticle;
use HeimrichHannot\NewsBundle\NewsList;

class HookListener
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

    /**
     * Modify the page or layout object
     *
     * @param \PageModel   $page
     * @param \LayoutModel $layout
     * @param \PageRegular $pageRegular
     */
    public function getPageLayout(\PageModel $page, \LayoutModel $layout, \PageRegular $pageRegular)
    {
        NewsList::resetSeen($page->id); // reset seen news articles on request
    }

    /**
     * Extend the news list count all items
     *
     * @param array     $newsArchives
     * @param bool|null $featured
     * @param \Module   $module
     *
     * @return int|boolean Return the number of total items or false if next hook should be triggered
     */
    public function newsListCountItems(array $newsArchives, $featured, \Module $module)
    {
        $objNewsList = new NewsList($newsArchives, $featured, $module);

        return $objNewsList->count();
    }


    /**
     * Extend fetch matching of news list items
     *
     * @param  array        $newsArchives
     * @param  boolean|null $featured
     * @param  integer      $limit
     * @param  integer      $offset
     * @param \Module       $module
     *
     * @return \Model\Collection|\NewsModel|null|false Return a collection of items or false if next hook should be triggered
     */
    public function newsListFetchItems(array $newsArchives, $featured, $limit, $offset, \Module $module)
    {
        $objNewsList = new NewsList($newsArchives, $featured, $module);

        return $objNewsList->fetch($limit, $offset);
    }


    /**
     * Extend news article data
     *
     * @param \FrontendTemplate $template
     * @param array             $article
     * @param \Module           $module
     */
    public function parseArticles(\FrontendTemplate $template, array $article, \Module $module)
    {
        $objArticle = new NewsArticle($template, $article, $module);
        $template   = $objArticle->getNewsTemplate();
    }
}
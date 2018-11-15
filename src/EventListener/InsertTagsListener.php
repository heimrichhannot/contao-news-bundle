<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use HeimrichHannot\NewsBundle\Model\NewsListModel;
use HeimrichHannot\NewsBundle\Model\NewsModel;

/**
 * Handles insert tags for news.
 *
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 */
class InsertTagsListener
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * @var array
     */
    private $supportedNewsTags = [
    ];

    /**
     * @var array
     */
    private $supportedNewsListTags = [
        'news_list',
        'news_list_url',
        'news_list_title',
    ];

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
     * Replaces calendar insert tags.
     *
     * @param string $tag
     *
     * @return string|false
     */
    public function onReplaceInsertTags($tag)
    {
        $elements = explode('::', $tag);
        $key = strtolower($elements[0]);

        if (\in_array($key, $this->supportedNewsTags, true)) {
            return $this->replaceNewsInsertTag($key, $elements[1]);
        }

        if (\in_array($key, $this->supportedNewsListTags, true)) {
            return $this->replaceNewsListInsertTag($key, $elements[1]);
        }

        return false;
    }

    /**
     * Replaces an news-related insert tag.
     *
     * @param string $insertTag
     * @param string $idOrAlias
     *
     * @return string
     */
    private function replaceNewsInsertTag($insertTag, $idOrAlias)
    {
        $this->framework->initialize();

        /** @var NewsModel $adapter */
        $adapter = $this->framework->getAdapter(NewsModel::class);

        if (null === ($news = $adapter->findByIdOrAlias($idOrAlias))) {
            return '';
        }

        return $this->generateNewsReplacement($news, $insertTag);
    }

    /**
     * Replaces an news list-related insert tag.
     *
     * @param string $insertTag
     * @param string $idOrAlias
     *
     * @return string
     */
    private function replaceNewsListInsertTag($insertTag, $idOrAlias)
    {
        $this->framework->initialize();

        /** @var NewsListModel $adapter */
        $adapter = $this->framework->getAdapter(NewsListModel::class);

        if (null === ($newsList = $adapter->findByIdOrAlias($idOrAlias))) {
            return '';
        }

        return $this->generateNewsListReplacement($newsList, $insertTag);
    }

    /**
     * Generates the replacement string.
     *
     * @param NewsModel $news
     * @param string    $insertTag
     *
     * @return string
     */
    private function generateNewsReplacement(NewsModel $news, $insertTag)
    {
        return '';
    }

    /**
     * Generates the replacement string.
     *
     * @param NewsListModel $newsList
     * @param string        $insertTag
     *
     * @return string
     */
    private function generateNewsListReplacement(NewsListModel $newsList, $insertTag)
    {
        switch ($insertTag) {
            case 'news_list':
                $url = NewsListModel::generateNewsListUrl($newsList);

                return sprintf('<a href="%s">%s</a>', $url, $newsList->title);

                break;

            case 'news_list_url':
                return NewsListModel::generateNewsListUrl($newsList);

                break;

            case 'news_list_title':
                return $newsList->title;

                break;
        }

        return '';
    }
}

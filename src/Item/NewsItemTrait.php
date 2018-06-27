<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Item;

use Contao\ArticleModel;
use Contao\CommentsModel;
use Contao\Config;
use Contao\ContentModel;
use Contao\Controller;
use Contao\Date;
use Contao\NewsArchiveModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Contao\UserModel;

trait NewsItemTrait
{
    /**
     * URL cache array.
     *
     * @var array
     */
    private static $urlCache = [];

    /**
     * Compile css class.
     *
     * @return string
     */
    public function getCssClass(): string
    {
        $values = [$this->cssClass];

        // list reader item has custom cssClass like first, last, even,odd
        if (property_exists($this, '_cssClass')) {
            $values[] = $this->_cssClass;
        }

        if ($this->featured) {
            $values[] = 'featured';
        }

        return implode(' ', $values);
    }

    /**
     * Compile the headline link.
     *
     * @return string
     */
    public function getLinkHeadline(): string
    {
        // Internal link
        if ('external' !== $this->source) {
            return sprintf('<a href="%s" title="%s" itemprop="url">%s%s</a>',
                $this->getDetailsUrl(),
                StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $this->headline), true),
                $this->headline,
                '');
        }

        // External link
        return sprintf('<a href="%s" title="%s"%s itemprop="url">%s</a>',
            $this->getExternalUrl(),
            \StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['open'], $this->getExternalUrl())),
            ($this->target ? ' target="_blank"' : ''),
            $this->headline);
    }

    /**
     * Compile the more link.
     *
     * @return string
     */
    public function getMore(): string
    {
        // Internal link
        if ('external' !== $this->source) {
            return sprintf('<a href="%s" title="%s" itemprop="url">%s%s</a>',
                $this->getDetailsUrl(),
                StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['readMore'], $this->headline), true),
                $GLOBALS['TL_LANG']['MSC']['more'],
                '<span class="invisible"> '.$this->headline.'</span>');
        }

        // External link
        return sprintf('<a href="%s" title="%s"%s itemprop="url">%s</a>',
            $this->getExternalUrl(),
            \StringUtil::specialchars(sprintf($GLOBALS['TL_LANG']['MSC']['open'], $this->getExternalUrl())),
            ($this->target ? ' target="_blank"' : ''),
            $GLOBALS['TL_LANG']['MSC']['more']);
    }

    /**
     * Get the news archive data.
     *
     * @return array
     */
    public function getArchive(): ?array
    {
        /**
         * @var NewsArchiveModel
         */
        $archiveModel = $this->getManager()->getFramework()->getAdapter(NewsArchiveModel::class);

        if (null === ($archive = $archiveModel->findByPk($this->pid))) {
            return null;
        }

        return $archive->row();
    }

    /**
     * Get details url and add archive.
     *
     * @return null|string
     */
    public function getDetailsUrlWithArchive(): ?string
    {
        $url = $this->getDetailsUrl();

        // Add the current archive parameter (news archive)
        if (System::getContainer()->get('huh.request')->query->has('month')) {
            $url .= '?month='.System::getContainer()->get('huh.request')->query->get('month');
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getDetailsUrl(bool $external = true): string
    {
        $cacheKey = 'id_'.$this->id;

        // Load the URL from cache
        if (isset(self::$urlCache[$cacheKey])) {
            return self::$urlCache[$cacheKey];
        }

        switch ($this->source) {
            // Link to an external page
            case 'external':
                return $external ? $this->getExternalUrl() : '';
            // Link to an internal page
            case 'internal':
                return $this->getInternalUrl();
            // Link to an article
            case 'article':
                return $this->getArticleUrl();
        }

        return $this->getDefaultUrl();
    }

    /**
     * Get the external news url source = 'external'.
     *
     * @return null|string
     */
    public function getExternalUrl(): ? string
    {
        $cacheKey = 'id_'.$this->id;

        // Load the URL from cache
        if (isset(self::$urlCache[$cacheKey])) {
            return self::$urlCache[$cacheKey];
        }

        if ('mailto:' == substr($this->url, 0, 7)) {
            self::$urlCache[$cacheKey] = StringUtil::encodeEmail($this->url);
        } else {
            self::$urlCache[$cacheKey] = ampersand($this->url);
        }

        return self::$urlCache[$cacheKey] ?? null;
    }

    /**
     * Get the internal news url source = 'internal'.
     *
     * @return null|string
     */
    public function getInternalUrl(): ? string
    {
        $cacheKey = 'id_'.$this->id;

        // Load the URL from cache
        if (isset(self::$urlCache[$cacheKey])) {
            return self::$urlCache[$cacheKey];
        }

        /**
         * @var PageModel
         */
        $pageModel = $this->getManager()->getFramework()->getAdapter(PageModel::class);

        if (null !== ($target = $pageModel->findByPk($this->jumpTo))) {
            self::$urlCache[$cacheKey] = ampersand($target->getFrontendUrl());
        }

        return self::$urlCache[$cacheKey] ?? null;
    }

    /**
     * Get the article news url source = 'article'.
     *
     * @return null|string
     */
    public function getArticleUrl(): ? string
    {
        $cacheKey = 'id_'.$this->id;

        // Load the URL from cache
        if (isset(self::$urlCache[$cacheKey])) {
            return self::$urlCache[$cacheKey];
        }

        /**
         * @var NewsArchiveModel
         * @var PageModel        $pageModel
         */
        $pageModel = $this->getManager()->getFramework()->getAdapter(PageModel::class);
        $articleModel = $this->getManager()->getFramework()->getAdapter(ArticleModel::class);

        if (null !== ($article = $articleModel->findByPk($this->articleId, ['eager' => true])) && null !== ($parentPage = $pageModel->findByPk($article->pid))) {
            self::$urlCache[$cacheKey] = ampersand($parentPage->getFrontendUrl('/articles/'.($article->alias ?: $article->id)));
        }

        return self::$urlCache[$cacheKey] ?? null;
    }

    /**
     * Get the default news url source = 'default'.
     *
     * @return null|string
     */
    public function getDefaultUrl(): ? string
    {
        $cacheKey = 'id_'.$this->id;

        // Load the URL from cache
        if (isset(self::$urlCache[$cacheKey])) {
            return self::$urlCache[$cacheKey];
        }

        /**
         * @var NewsArchiveModel
         * @var PageModel        $pageModel
         */
        $pageModel = $this->getManager()->getFramework()->getAdapter(PageModel::class);
        $archiveModel = $this->getManager()->getFramework()->getAdapter(NewsArchiveModel::class);

        if (null === ($archive = $archiveModel->findByPk($this->pid))) {
            return null;
        }

        $page = $pageModel->findByPk($archive->jumpTo);

        if (null === $page) {
            self::$urlCache[$cacheKey] = ampersand(System::getContainer()->get('request_stack')->getCurrentRequest()->getRequestUri(), true);
        } else {
            self::$urlCache[$cacheKey] = ampersand($page->getFrontendUrl((Config::get('useAutoItem') ? '/' : '/items/').($this->alias ?: $this->id)));
        }

        return self::$urlCache[$cacheKey] ?? null;
    }

    /**
     * Get news date DateTime.
     *
     * @return string
     */
    public function getDatetime(): string
    {
        return Date::parse('Y-m-d\TH:i:sP', $this->date);
    }

    /**
     * Get news date timestamp.
     *
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->date;
    }

    /**
     * Get the author.
     *
     * @return null|string
     */
    public function getAuthor(): ?string
    {
        /** @var UserModel $adapter */
        $adapter = $this->getManager()->getFramework()->getAdapter(UserModel::class);

        if (null !== ($user = $adapter->findByPk($this->author))) {
            return $GLOBALS['TL_LANG']['MSC']['by'].' '.$user->name;
        }

        return null;
    }

    /**
     * Compile comment count.
     *
     * @return null|string
     */
    public function getCommentCount(): ?string
    {
        $total = $this->getNumberOfComments();

        return $total > 0 ? sprintf($GLOBALS['TL_LANG']['MSC']['commentCount'], $total) : '';
    }

    /**
     * Get number of comments.
     *
     * @return int|null
     */
    public function getNumberOfComments(): ?int
    {
        if ($this->noComments || !\in_array('comments', \ModuleLoader::getActive(), true) || 'default' != $this->source) {
            return null;
        }

        $total = CommentsModel::countPublishedBySourceAndParent($this->getDataContainer(), $this->id);

        return $total;
    }

    /**
     * Get formatted meta date.
     *
     * @return string
     */
    public function getDate(): string
    {
        global $objPage;

        return Date::parse($objPage->datimFormat, $this->date);
    }

    /**
     * Get all enclosures.
     *
     * @return array|null
     */
    public function getEnclosures(): ? array
    {
        if (true === $this->addEnclosure) {
            return null;
        }

        $template = new \stdClass();
        Controller::addEnclosuresToTemplate($template, $this->getRaw());

        return $template->enclosure;
    }

    /**
     * Compile the news text.
     *
     * @return string
     */
    public function getText(): string
    {
        $strText = '';

        /**
         * @var ContentModel
         */
        $adapter = $this->getManager()->getFramework()->getAdapter(ContentModel::class);

        if (null !== ($elements = $adapter->findPublishedByPidAndTable($this->id, $this->getDataContainer()))) {
            foreach ($elements as $element) {
                try {
                    $strText .= Controller::getContentElement($element->id);
                } catch (\ErrorException $e) {
                }
            }
        }

        return $strText;
    }

    /**
     * Check if the news has text.
     *
     * @return bool
     */
    public function hasText(): bool
    {
        // Display the "read more" button for external/article links
        if ('default' !== $this->source) {
            return true;
        }

        /**
         * @var ContentModel
         */
        $adapter = $this->getManager()->getFramework()->getAdapter(ContentModel::class);

        return $adapter->countPublishedByPidAndTable($this->id, $this->getDataContainer()) > 0;
    }

    /**
     * Check if the news has teaser text.
     *
     * @return bool
     */
    public function hasTeaser(): bool
    {
        return '' !== $this->teaser;
    }

    /**
     * Compile the teaser text.
     *
     * @return string
     */
    public function getTeaser(): string
    {
        return StringUtil::encodeEmail(StringUtil::toHtml5($this->teaser));
    }

    /**
     * Compile the newsHeadline.
     *
     * @return string
     */
    public function getNewsHeadline(): string
    {
        return $this->headline;
    }

    /**
     * Compile the news SubHeadline.
     *
     * @return string
     */
    public function getNewsSubHeadline(): string
    {
        return $this->subheadline;
    }

    /**
     * Check if the news has a subHeadline.
     *
     * @return bool
     */
    public function hasSubHeadline(): bool
    {
        return '' !== $this->subheadline;
    }
}

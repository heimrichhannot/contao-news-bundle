<?php

/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\EventListener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use HeimrichHannot\NewsBundle\NewsModel;

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
    private $supportedTags = [
        'news_info_box',
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
        $key      = strtolower($elements[0]);

        if (in_array($key, $this->supportedTags, true)) {
            return $this->replaceNewsInsertTag($key, $elements[1]);
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

        /** @var \Contao\NewsModel $adapter */
        $adapter = $this->framework->getAdapter(NewsModel::class);

        if (null === ($news = $adapter->findByIdOrAlias($idOrAlias))) {
            return '';
        }

        return $this->generateReplacement($news, $insertTag);
    }

    /**
     * Generates the replacement string.
     *
     * @param \Contao\NewsModel $news
     * @param string $insertTag
     *
     * @return string
     */
    private function generateReplacement(\Contao\NewsModel $news, $insertTag)
    {
        switch ($insertTag) {

            case 'news_info_box':
                return '[[INFOBOX]]';
        }

        return '';
    }
}

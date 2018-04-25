<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Item;

use Contao\ContentModel;
use Contao\Controller;
use Contao\StringUtil;

class DefaultItem extends \HeimrichHannot\ReaderBundle\Item\DefaultItem
{
    /**
     * Compile the news text.
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
                $strText .= Controller::getContentElement($element->id);
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
        return $this->newsHeadline;
    }

    /**
     * Check if the news has a subHeadline.
     *
     * @return bool
     */
    public function hasSubHeadline(): bool
    {
        return '' !== $this->subHeadLine;
    }
}

<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Backend;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\DataContainer;
use Contao\Model;
use Contao\NewsArchiveModel;
use Contao\NewsModel;
use Contao\System;

class News
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Manipulate related news from `tl_news.related_news` remote tagsinput call.
     *
     * @param                $arrOption
     * @param \DataContainer $dc
     */
    public function getRelatedNews($arrOption, DataContainer $dc)
    {
        if ($arrOption['value'] == $dc->id) {
            return null;
        }

        return $arrOption;
    }

    /**
     * get member by last name from input.
     *
     * @param \DataContainer $dc
     *
     * @return array
     */
    public function getMembers($arrOption, DataContainer $dc)
    {
        if ($arrOption['value'] == $dc->id) {
            return null;
        }

        return $arrOption;
    }

    /**
     * If news archive has addCustomNewsPalettes set and a customNewsPalettes given,
     * replace the default news palette with the given one.
     */
    public function onLoad(DataContainer $dc)
    {
        /** @var NewsModel $news */
        if (null === ($news = $this->framework->getAdapter(Model::getClassFromTable('tl_news'))->findByPk($dc->id))) {
            return;
        }

        /** @var NewsArchiveModel $archive */
        if (null === ($archive = $this->framework->getAdapter(Model::getClassFromTable('tl_news_archive'))->findByPk($news->pid))) {
            return;
        }

        $this->initCustomPalette($news, $archive, $dc);
        $this->limitInputCharacterLength($news, $archive, $dc);
    }

    /**
     * Limit input character length for editors based on archive config.
     *
     * @param NewsModel        $news
     * @param NewsArchiveModel $archive
     */
    protected function limitInputCharacterLength(Model $news, Model $archive, DataContainer $dc): bool
    {
        if (false === (bool) $archive->limitInputCharacterLength) {
            return false;
        }

        if (empty($limits = \Contao\StringUtil::deserialize($archive->inputCharacterLengths, true))) {
            return false;
        }

        foreach ($limits as $limit) {
            $strField = $limit['field'];
            $intLength = $limit['length'];

            if ($intLength > 0 && isset($GLOBALS['TL_DCA']['tl_news']['fields'][$strField])) {
                $arrData = &$GLOBALS['TL_DCA']['tl_news']['fields'][$strField];

                if (isset($arrData['eval']['maxlength'])) {
                    unset($arrData['eval']['maxlength']); // contao core does not count special characters as decoded entities
                }
                $arrData['eval']['data-maxlength'] = $intLength;
                $arrData['eval']['rgxp'] = 'maxlength::'.$intLength;
                $arrData['eval']['data-count-characters'] = true;
                $arrData['eval']['data-count-characters-text'] = $GLOBALS['TL_LANG']['MSC']['countCharactersRemaing'];

                if ($arrData['eval']['rte']) {
                    $arrData['eval']['rte'] = 'tinyMCELimitedInputCharacterLength|html';
                }
            }
        }

        return true;
    }

    /**
     * If news archive has addCustomNewsPalettes set and a customNewsPalettes given,
     * replace the default news palette with the given one.
     *
     * @param NewsModel        $news
     * @param NewsArchiveModel $archive
     */
    protected function initCustomPalette(Model $news, Model $archive, DataContainer $dc): bool
    {
        if (false === (bool) $archive->addCustomNewsPalettes) {
            return false;
        }

        if ('' === $archive->customNewsPalettes) {
            return false;
        }

        if (!isset($GLOBALS['TL_DCA']['tl_news']['palettes'][$archive->customNewsPalettes])) {
            return false;
        }

        $GLOBALS['TL_DCA']['tl_news']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_news']['palettes'][$archive->customNewsPalettes];

        // HOOK: loadDataContainer must be triggerd after onload_callback, otherwise slick slider wont work anymore
        if (isset($GLOBALS['TL_HOOKS']['loadDataContainer']) && \is_array($GLOBALS['TL_HOOKS']['loadDataContainer'])) {
            foreach ($GLOBALS['TL_HOOKS']['loadDataContainer'] as $callback) {
                System::importStatic($callback[0])->{$callback[1]}($dc->table);
            }
        }

        return true;
    }
}

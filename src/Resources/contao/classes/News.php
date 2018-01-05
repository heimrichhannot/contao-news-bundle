<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\NewsBundle;


use Contao\DataContainer;

class News extends \Contao\News
{
    const XHR_READER_SURVEY_RESULT_ACTION = 'showReadersSurveyResultAction';

    const XHR_GROUP = 'hh_news_bundle';

    const XHR_PARAMETER_ID    = 'id';
    const XHR_PARAMETER_ITEMS = 'items';


    /**
     * If news archive has addCustomNewsPalettes set and a customNewsPalettes given,
     * replace the default news palette with the given one
     *
     * @param DataContainer $dc
     *
     * @return bool
     */
    public function initCustomPalette(DataContainer $dc)
    {
        $objNews = \HeimrichHannot\NewsBundle\Model\NewsModel::findByPk($dc->id);
        if ($objNews === null) {
            return false;
        }
        $objArchive = $objNews->getRelated('pid');
        if ($objArchive === null) {
            return false;
        }
        if ($objArchive->addCustomNewsPalettes && $objArchive->customNewsPalettes != '') {
            if (!isset($GLOBALS['TL_DCA']['tl_news']['palettes'][$objArchive->customNewsPalettes])) {
                return false;
            }
            $GLOBALS['TL_DCA']['tl_news']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_news']['palettes'][$objArchive->customNewsPalettes];
        }
        // HOOK: loadDataContainer must be triggerd after onload_callback, otherwise slick slider wont work anymore
        if (isset($GLOBALS['TL_HOOKS']['loadDataContainer']) && is_array($GLOBALS['TL_HOOKS']['loadDataContainer'])) {
            foreach ($GLOBALS['TL_HOOKS']['loadDataContainer'] as $callback) {
                $this->import($callback[0]);
                $this->{$callback[0]}->{$callback[1]}($dc->table);
            }
        }
    }
}
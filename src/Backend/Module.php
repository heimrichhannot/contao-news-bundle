<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Backend;

use Contao\DataContainer;
use HeimrichHannot\NewsBundle\HeimrichHannotContaoNewsBundle;

class Module extends \Backend
{
    /**
     * Modify data container config.
     *
     * @param \DataContainer $dc
     */
    public function modifyDC(\DataContainer $dc)
    {
        $objModule = \ModuleModel::findByPk($dc->id);

        if (null === $objModule) {
            return;
        }

        $dca = &$GLOBALS['TL_DCA']['tl_module'];

        switch ($objModule->type) {
            case 'newslist_related':
                $dca['fields']['customTpl']['options'] = $this->getTemplateGroup('mod_newslist');
                unset($dca['fields']['customTpl']['options_callback']);
                break;
            case HeimrichHannotContaoNewsBundle::MODULE_NEWSLIST:
                break;
            case 'newsreader':
                break;
        }
    }

    /**
     * Get all news list filters as list.
     *
     * @param \DataContainer $dc
     *
     * @return array List of all news filters
     */
    public function getNewsListFilters(\DataContainer $dc)
    {
        $filters = \System::getContainer()->get('huh.news.list_filter.registry')->getAliases();

        $options = [];

        foreach ($filters as $alias) {
            $options[$alias] = $alias;
        }

        return $options;
    }

    /**
     * Get all news list modules as list.
     *
     * @param \DataContainer $dc
     *
     * @return array List of all news list modules
     */
    public function getNewsListFilterModules(\DataContainer $dc)
    {
        $options = static::getModuleOptions('newslist_filter');

        unset($options[$dc->id]);

        return $options;
    }

    /**
     * Get all related modules as list.
     *
     * @param \DataContainer $dc
     *
     * @return array List of all related modules
     */
    public function getNewsListRelatedModules(\DataContainer $dc)
    {
        $options = static::getModuleOptions('newslist_related');

        unset($options[$dc->id]);

        return $options;
    }

    /**
     * Get all news reader survey modules as list.
     *
     * @param \DataContainer $dc
     *
     * @return array List of all reader survey modules
     */
    public function getNewsReadersSurveyModules(\DataContainer $dc)
    {
        return static::getModuleOptions('news_readers_survey');
    }

    /**
     * Get all news reader survey result modules as list.
     *
     * @param \DataContainer $dc
     *
     * @return array List of all reader survey result modules
     */
    public function getNewsReadersSurveyResultModules(\DataContainer $dc)
    {
        return static::getModuleOptions('news_readers_survey_result');
    }

    /**
     * Get all news info box modules as list.
     *
     * @param \DataContainer $dc
     *
     * @return array List of all news info box modules
     */
    public function getNewsInfoBoxModules(\DataContainer $dc)
    {
        return static::getModuleOptions('news_info_box');
    }

    public function getNewsListModules(DataContainer $dc)
    {
        return static::getModuleOptions(HeimrichHannotContaoNewsBundle::MODULE_NEWSLIST);
    }

    public function getNewsNavigationModules(DataContainer $dc)
    {
        return static::getModuleOptions(HeimrichHannotContaoNewsBundle::MODULE_NEWSNAVIGATION);
    }

    /**
     * Get all modules for a given type as list.
     *
     * @param string $strType The module type
     *
     * @return array List of modules
     */
    protected static function getModuleOptions($strType)
    {
        $arrOptions = [];

        $objModules = \ModuleModel::findByType($strType);

        if (null === $objModules) {
            return $arrOptions;
        }

        return $objModules->fetchEach('name');
    }
}

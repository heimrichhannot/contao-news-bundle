<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 08.06.17
 * Time: 13:42
 */

namespace HeimrichHannot\NewsBundle\Backend;


class Module extends \Backend
{
    /**
     * Modify data container config
     *
     * @param \DataContainer $dc
     */
    public function modifyDC(\DataContainer $dc)
    {
        $objModule = \ModuleModel::findByPk($dc->id);

        if ($objModule === null) {
            return;
        }

        $dca = &$GLOBALS['TL_DCA']['tl_module'];

        switch ($objModule->type)
        {
            case 'newslist_related':
                $dca['fields']['customTpl']['options'] = $this->getTemplateGroup('mod_newslist');
                unset($dca['fields']['customTpl']['options_callback']);
                break;
            case 'newslist':
                break;
            case 'newsreader':
                break;
        }
    }

    public function getNewsListRelatedModules(\DataContainer $dc)
    {
        $options = static::getModuleOptions('newslist_related');

        unset($options[$dc->id]);

        return $options;
    }

    public function getNewsReadersSurveyModules()
    {
        return static::getModuleOptions('news_readers_survey');
    }

    public function getNewsReadersSurveyResultModules()
    {
        return static::getModuleOptions('news_readers_survey_result');
    }

    public function getNewsInfoBoxModules()
    {
        return static::getModuleOptions('news_info_box');
    }

    protected static function getModuleOptions($strType)
    {
        $arrOptions = [];

        $objModules = \ModuleModel::findByType($strType);

        if ($objModules === null) {
            return $arrOptions;
        }

        return $objModules->fetchEach('name');
    }
}
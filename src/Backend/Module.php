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

        switch ($objModule->type) {
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

    /**
     * Get all related modules as list
     * @param \DataContainer $dc
     * @return array List of all related modules
     */
    public function getNewsListRelatedModules(\DataContainer $dc)
    {
        $options = static::getModuleOptions('newslist_related');

        unset($options[$dc->id]);

        return $options;
    }

    /**
     * Get all news reader survey modules as list
     * @param \DataContainer $dc
     * @return array List of all reader survey modules
     */
    public function getNewsReadersSurveyModules(\DataContainer $dc)
    {
        return static::getModuleOptions('news_readers_survey');
    }

    /**
     * Get all news reader survey result modules as list
     * @param \DataContainer $dc
     * @return array List of all reader survey result modules
     */
    public function getNewsReadersSurveyResultModules(\DataContainer $dc)
    {
        return static::getModuleOptions('news_readers_survey_result');
    }

    /**
     * Get all news info box modules as list
     * @param \DataContainer $dc
     * @return array List of all news info box modules
     */
    public function getNewsInfoBoxModules(\DataContainer $dc)
    {
        return static::getModuleOptions('news_info_box');
    }

    /**
     * Get all modules for a given type as list
     * @param string $strType The module type
     *
     * @return array List of modules
     */
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
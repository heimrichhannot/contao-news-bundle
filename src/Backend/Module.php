<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 08.06.17
 * Time: 13:42
 */

namespace HeimrichHannot\NewsBundle\Backend;


use HeimrichHannot\NewsBundle\NewsModel;

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
                $metaFields = deserialize($objModule->news_metaFields, true);

                if (in_array('tags', $metaFields))
                {
                    $dca['palettes']['newslist'] = str_replace('{template_legend', '{tags_legend},addNewsTagFilter;{template_legend', $dca['palettes']['newslist']);
                }

                break;
            case 'newsreader':
                $metaFields = deserialize($objModule->news_metaFields, true);

                if (in_array('tags', $metaFields))
                {
                    $dca['palettes']['newsreader'] = str_replace('{template_legend', '{tags_legend},newsTagFilterJumpTo;{template_legend', $dca['palettes']['newsreader']);
                }

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
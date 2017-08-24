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

        if ($objModules === null)
        {
            return $arrOptions;
        }

        return $objModules->fetchEach('name');
    }
}
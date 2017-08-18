<?php

$dc = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Selectors
 */
$dc['palettes']['__selector__'][] = 'use_news_lists';

/**
 * Palettes
 */
$dc['palettes']['news_contact_box'] =
    '{title_legend},name,headline,type;{config_legend},news_archives;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dc['palettes']['news_readers_survey'] =
    '{title_legend},name,headline,type;{config_legend},news_archives;{news_readers_survey_result_legend},news_readers_survey_result;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dc['palettes']['news_readers_survey_result'] =
    '{title_legend},name,headline,type;{config_legend},news_archives;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dc['palettes']['news_info_box'] =
    '{title_legend},name,headline,type;{config_legend},news_archives;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dc['palettes']['newslist'] = str_replace('news_archives', 'news_archives,use_news_lists', $dc['palettes']['newslist']);

/**
 * Subpalettes
 */
$dc['subpalettes']['use_news_lists'] = 'news_lists';

/**
 * Fields
 */
$arrFields = [
    'use_news_lists'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['use_news_lists'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'clr', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'news_lists'                 => [
        'label'      => &$GLOBALS['TL_LANG']['tl_module']['news_lists'],
        'exclude'    => true,
        'inputType'  => 'checkboxWizard',
        'foreignKey' => 'tl_news_list.title',
        'relation'   => ['type' => 'hasMany', 'load' => 'eager'],
        'eval'       => ['multiple' => true],
        'sql'        => "blob NULL",
    ],
    'news_readers_survey_result' => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_readers_survey_result'],
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\NewsBundle\Backend\Module', 'getNewsReadersSurveyResultModules'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true],
        'sql'              => "int(10) NOT NULL default '0'",
    ],
];

$dc['fields'] = array_merge($dc['fields'], $arrFields);
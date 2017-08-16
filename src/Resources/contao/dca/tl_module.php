<?php

$dc = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Selectors
 */


/**
 * Palettes
 */
$dc['palettes']['ls_suggest'] =
    '{title_legend},name,headline,type;{redirect_legend},jumpTo;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dc['palettes']['news_contact_box'] =
    '{title_legend},name,headline,type;{config_legend},news_archives;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dc['palettes']['news_readers_survey'] =
    '{title_legend},name,headline,type;{config_legend},news_archives;{news_readers_survey_result_legend},news_readers_survey_result;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dc['palettes']['news_readers_survey_result'] =
    '{title_legend},name,headline,type;{config_legend},news_archives;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dc['palettes']['news_info_box'] =
    '{title_legend},name,headline,type;{config_legend},news_archives;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

/**
 * Subpalettes
 */


/**
 * Fields
 */
$arrFields = [
    'ls_search_module'           => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['ls_search_module'],
        'inputType'        => 'select',
        'options_callback' => ['Dav\LawyerSearchBundle\Backend\Module', 'getSearchModules'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true],
        'sql'              => "int(10) NOT NULL default '0'",
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
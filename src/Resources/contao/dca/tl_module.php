<?php

$dca = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Config
 */
$dca['config']['onload_callback']['huh.newsbundle'] = ['HeimrichHannot\NewsBundle\Backend\Module', 'modifyDC'];

/**
 * Selectors
 */
$dca['palettes']['__selector__'][] = 'use_news_lists';
$dca['palettes']['__selector__'][] = 'newsListMode';
$dca['palettes']['__selector__'][] = 'add_related_news';

/**
 * Palettes
 */
$dca['palettes']['news_contact_box'] =
    '{title_legend},name,headline,type;{config_legend},news_archives;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dca['palettes']['news_readers_survey'] =
    '{title_legend},name,headline,type;{config_legend},news_archives;{news_readers_survey_result_legend},news_readers_survey_result;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dca['palettes']['news_readers_survey_result'] =
    '{title_legend},name,headline,type;{config_legend},news_archives;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dca['palettes']['news_info_box'] =
    '{title_legend},name,headline,type;{config_legend},news_archives;{redirect_legend},jumpTo;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dca['palettes']['newslist'] = str_replace('news_archives', 'news_archives,use_news_lists,skipPreviousNews', $dca['palettes']['newslist']);

$dca['palettes']['newslist'] = str_replace('{template_legend', '{news_related_legend},add_related_news;{template_legend', $dca['palettes']['newslist']);


$dca['palettes']['newslist_related'] = str_replace('{news_related_legend},add_related_news;', '', $dca['palettes']['newslist']);

$dca['palettes']['newsreader'] = str_replace('customTpl;', 'customTpl;{news_info_box_legend},newsInfoBoxModule;', $dca['palettes']['newsreader']);
$dca['palettes']['newsreader'] = str_replace('{template_legend', '{news_related_legend},add_related_news;{template_legend', $dca['palettes']['newsreader']);

// update slick_newslist because already invoked
$dca['palettes']['slick_newslist'] = $dca['palettes']['newslist'];

/**
 * Subpalettes
 */
$dca['subpalettes']['use_news_lists']                                                           = 'newsListMode';
$dca['subpalettes']['newsListMode_' . \HeimrichHannot\NewsBundle\Backend\NewsList::MODE_MANUAL] = 'news_lists';
$dca['subpalettes']['add_related_news']                                                         = 'related_news_module';


/**
 * Fields
 */
$fields = [
    'use_news_lists'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['use_news_lists'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'newsListMode'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['newsListMode'],
        'exclude'   => true,
        'filter'    => true,
        'inputType' => 'select',
        'options'   => \HeimrichHannot\NewsBundle\Backend\NewsList::MODES,
        'reference' => &$GLOBALS['TL_LANG']['tl_module']['reference']['newsBundle'],
        'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
        'sql'       => "varchar(64) NOT NULL default '" . \HeimrichHannot\NewsBundle\Backend\NewsList::MODE_MANUAL . "'"
    ],
    'news_lists'                 => [
        'label'      => &$GLOBALS['TL_LANG']['tl_module']['news_lists'],
        'exclude'    => true,
        'inputType'  => 'checkboxWizard',
        'foreignKey' => 'tl_news_list.title',
        'relation'   => ['type' => 'hasMany', 'load' => 'eager'],
        'eval'       => ['multiple' => true, 'mandatory' => true],
        'sql'        => "blob NULL",
    ],
    'news_readers_survey_result' => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['news_readers_survey_result'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\NewsBundle\Backend\Module', 'getNewsReadersSurveyResultModules'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true],
        'sql'              => "int(10) NOT NULL default '0'",
    ],
    'add_related_news'           => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['add_related_news'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'clr', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'related_news_module'        => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['related_news_module'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\NewsBundle\Backend\Module', 'getNewsListRelatedModules'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'mandatory' => true],
        'sql'              => "int(10) NOT NULL default '0'",
    ],
    'skipPreviousNews'           => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['skipPreviousNews'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'clr'],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'newsInfoBoxModule'          => [
        'inputType'        => 'select',
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['newsInfoBoxModule'],
        'options_callback' => ['HeimrichHannot\NewsBundle\Backend\Module', 'getNewsInfoBoxModules'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true],
        'sql'              => "int(1) NOT NULL default '0'",
    ],
];

$dca['fields']['news_metaFields']['options'][] = 'writers';
$dca['fields']['news_metaFields']['options'][] = 'tags';

$dca['fields'] = array_merge($dca['fields'], $fields);
<?php

$dc = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Config
 */
$dc['config']['onload_callback']['huh.newsbundle'] = ['HeimrichHannot\NewsBundle\Backend\Module', 'modifyDC'];

/**
 * Selectors
 */
$dc['palettes']['__selector__'][] = 'use_news_lists';
$dc['palettes']['__selector__'][] = 'add_related_news';
$dc['palettes']['__selector__'][] = 'news_slick_box_selector';


/**
 * Palettes
 */
$dc['palettes']['news_contact_box'] = '{title_legend},name,headline,type;{config_legend},news_archives;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dc['palettes']['news_readers_survey'] = '{title_legend},name,headline,type;{config_legend},news_archives;{news_readers_survey_result_legend},news_readers_survey_result;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dc['palettes']['news_readers_survey_result'] = '{title_legend},name,headline,type;{config_legend},news_archives;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dc['palettes']['news_info_box'] = '{title_legend},name,headline,type;{config_legend},news_archives;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dc['palettes']['newslist'] = str_replace('news_archives', 'news_archives,use_news_lists,skipPreviousNews', $dc['palettes']['newslist']);

$dc['palettes']['newslist'] = str_replace('{template_legend', '{news_related_legend},add_related_news;{template_legend', $dc['palettes']['newslist']);


$dc['palettes']['newslist_related'] = str_replace('{news_related_legend},add_related_news;', '', $dc['palettes']['newslist']);

// update slick_newslist because already invoked
$dc['palettes']['slick_newslist'] = $dc['palettes']['newslist'];

$dc['palettes']['news_suggestions'] = '{title_legend},name,headline,type;{config_legend},news_archives,perPage;{news_suggestion_legend},news_suggestion;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

/**
 * Subpalettes
 */
$dc['subpalettes']['use_news_lists']   = 'news_lists';
$dc['subpalettes']['add_related_news'] = 'related_news_module';


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
    'news_suggestion'            => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['news_suggestion'],
        'inputType' => 'multiColumnEditor',
        'eval'      => [
            'multiColumnEditor' => [
                'sortable'    => true,
                // set to 0 if it should also be possible to have *no* row (default: 1)
                'class'       => 'news_suggestion',
                'minRowCount' => 2,
                // set to 0 if an infinite number of rows should be possible (default: 0)
                'maxRowCount' => 0,
                'fields'      => [
                    'suggestion_label'        => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_module']['suggestion_label'],
                        'inputType' => 'text',
                        'eval'      => ['groupStyle' => 'width:250px', 'mandatory' => true],
                    ],
                    'suggestion_order_column' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_module']['suggestion_order_column'],
                        'inputType' => 'text',
                        'eval'      => ['groupStyle' => 'width:250px', 'mandatory' => true],
                    ],
                ],
            ],
        ],
        'sql'       => "blob NULL",
    ],
];

$dc['fields']['news_metaFields']['options'][] = 'writers';
$dc['fields']['news_metaFields']['options'][] = 'tags';

$dc['fields'] = array_merge($dc['fields'], $arrFields);
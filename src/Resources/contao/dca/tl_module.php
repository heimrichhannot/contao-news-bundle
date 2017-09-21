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
$dca['palettes']['__selector__'][] = 'addCustomSort';


/**
 * Palettes
 */
$dca['palettes']['news_contact_box'] = '{title_legend},name,headline,type;{config_legend},news_archives;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dca['palettes']['news_readers_survey'] = '{title_legend},name,headline,type;{config_legend},news_archives;{news_readers_survey_result_legend},news_readers_survey_result;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dca['palettes']['news_readers_survey_result'] = '{title_legend},name,headline,type;{config_legend},news_archives;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dca['palettes']['news_info_box'] = '{title_legend},name,headline,type;{config_legend},news_archives;{redirect_legend},jumpTo;{template_legend:hide},customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

$dca['palettes']['newslist'] = str_replace('news_archives', 'news_archives,newsListFilterModule,use_news_lists,skipPreviousNews,addCustomSort', $dca['palettes']['newslist']);

$dca['palettes']['newslist'] = str_replace('{template_legend', '{tags_legend},addNewsTagFilter,newsTagFilterJumpTo;{news_related_legend},add_related_news;{template_legend', $dca['palettes']['newslist']);

$dca['palettes']['newslist']    = str_replace(',imgSize', ',imgSize,useTeaserImage,posterSRC', $dca['palettes']['newslist']);
$dca['palettes']['newsreader']  = str_replace(',imgSize', ',imgSize,useTeaserImage,posterSRC', $dca['palettes']['newsreader']);
$dca['palettes']['newsarchive'] = str_replace(',imgSize', ',imgSize,useTeaserImage,posterSRC', $dca['palettes']['newsarchive']);

$dca['palettes']['newslist_related'] = str_replace('{news_related_legend},add_related_news;', '', $dca['palettes']['newslist']);

$dca['palettes']['newsreader'] = str_replace('customTpl;', 'customTpl;{news_info_box_legend},newsInfoBoxModule;', $dca['palettes']['newsreader']);
$dca['palettes']['newsreader'] = str_replace('{template_legend', '{tags_legend},newsTagFilterJumpTo;{news_related_legend},add_related_news;{template_legend', $dca['palettes']['newsreader']);

$dca['palettes']['newslist_filter'] = '{title_legend},name,headline,type;{config_legend},news_archives,newsListFilters;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';


// update slick_newslist because already invoked
$dca['palettes']['slick_newslist'] = $dca['palettes']['newslist'];

/**
 * Subpalettes
 */
$dca['subpalettes']['use_news_lists']                                                           = 'newsListMode';
$dca['subpalettes']['newsListMode_' . \HeimrichHannot\NewsBundle\Backend\NewsList::MODE_MANUAL] = 'news_lists';
$dca['subpalettes']['add_related_news']                                                         = 'related_news_module';
$dca['subpalettes']['addCustomSort']                                                            = 'sortClause';


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
        'sql'       => "varchar(64) NOT NULL default '" . \HeimrichHannot\NewsBundle\Backend\NewsList::MODE_MANUAL . "'",
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
    'addNewsTagFilter'           => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addNewsTagFilter'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'newsTagFilterJumpTo'        => [
        'label'      => &$GLOBALS['TL_LANG']['tl_module']['newsTagFilterJumpTo'],
        'exclude'    => true,
        'inputType'  => 'pageTree',
        'foreignKey' => 'tl_page.title',
        'eval'       => ['fieldType' => 'radio', 'tl_class' => 'clr'],
        'sql'        => "int(10) unsigned NOT NULL default '0'",
        'relation'   => ['type' => 'hasOne', 'load' => 'eager'],
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
    'addCustomSort'              => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['addCustomSort'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'clr', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'sortClause'                 => [
        'label'       => &$GLOBALS['TL_LANG']['tl_module']['sortClause'],
        'inputType'   => 'textarea',
        'exclude'     => true,
        'eval'        => ['class' => 'monospace', 'rte' => 'ace', 'tl_class' => 'clr long'],
        'explanation' => 'insertTags',
        'sql'         => "text NULL",
    ],
    'useTeaserImage'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['useTeaserImage'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'clr'],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'posterSRC'                  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['posterSRC'],
        'exclude'   => true,
        'inputType' => 'fileTree',
        'eval'      => ['filesOnly' => true, 'fieldType' => 'radio'],
        'sql'       => "binary(16) NULL"
    ],
    'newsListFilterModule'       => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['newsListFilterModule'],
        'exclude'          => true,
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\NewsBundle\Backend\Module', 'getNewsListFilterModules'],
        'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true],
        'sql'              => "int(10) NOT NULL default '0'",
    ],
    'newsListFilters'            => [
        'label'            => &$GLOBALS['TL_LANG']['tl_module']['newsListFilters'],
        'exclude'          => true,
        'inputType'        => 'checkboxWizard',
        'options_callback' => ['HeimrichHannot\NewsBundle\Backend\Module', 'getNewsListFilters'],
        'eval'             => ['tl_class' => 'w50', 'multiple' => true],
        'sql'              => "blob NULL",
    ]
];

$dca['fields']['news_metaFields']['options'][]              = 'writers';
$dca['fields']['news_metaFields']['options'][]              = 'tags';
$dca['fields']['news_metaFields']['options'][]              = 'ratings';

$dca['fields'] = array_merge($dca['fields'], $fields);
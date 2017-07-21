<?php

$dc = &$GLOBALS['TL_DCA']['tl_news'];

/**
 * Selectors
 */


/**
 * Palettes
 */
$dc['palettes']['n_expert'] =
    '{title_legend},name,headline,type;{template_legend:hide},jumpTo,customTpl;{config_legend},n_expert;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

/**
 * Subpalettes
 */


/**
 * Fields
 */
$arrFields = [
    'n_expert' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['n_expert'],
        'inputType' => 'tagsinput',
        //        'options_callback' => ['Dav\LawyerSearchBundle\Backend\Module', 'getDistanceChoices'],
        'sql'       => "blob NULL",
        'options'   => ['boston', 'berlin', 'hamburg', 'london'],
        'eval'      => [
            'freeInput' => false,
            'multiple'  => true,
        ],
    ],
];

$dc['fields'] = array_merge($dc['fields'], $arrFields);
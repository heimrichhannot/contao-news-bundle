<?php

$dca = &$GLOBALS['TL_DCA']['tl_settings'];

/**
 * Palettes
 */
$dca['palettes']['default'] .= ';{tags_legend},tagSourceJumpTos;';

/**
 * Fields
 */
$fields = [
    'tagSourceJumpTos' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_settings']['tagSourceJumpTos'],
        'inputType' => 'multiColumnEditor',
        'eval'      => [
            'multiColumnEditor' => [
                'minRowCount' => 0,
                'fields'      => [
                    'source' => [
                        'label'      => &$GLOBALS['TL_LANG']['tl_settings']['reference']['newsBundle']['source'],
                        'exclude'    => true,
                        'filter'     => true,
                        'inputType'  => 'select',
                        'options_callback' => ['HeimrichHannot\NewsBundle\Model\CfgTagModel', 'getSourcesAsOptions'],
                        'eval'       => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true, 'groupStyle' => 'width: 200px'],
                        'sql'        => "varchar(64) NOT NULL default ''"
                    ],
                    'jumpTo' => [
                        'label'      => &$GLOBALS['TL_LANG']['tl_settings']['reference']['newsBundle']['jumpTo'],
                        'exclude'    => true,
                        'inputType'  => 'pageTree',
                        'foreignKey' => 'tl_page.title',
                        'eval'       => ['fieldType' => 'radio', 'groupStyle' => 'width: 200px'],
                        'sql'        => "int(10) unsigned NOT NULL default '0'",
                        'relation'   => ['type' => 'hasOne', 'load' => 'eager']
                    ],
                ],
            ],
        ]
    ],
];

$dca['fields'] += $fields;
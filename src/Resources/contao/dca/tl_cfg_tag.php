<?php

$dc = &$GLOBALS['TL_DCA']['tl_cfg_tag'];

/**
 * Palettes
 */
$dc['palettes']['default'] = str_replace('source', 'source,reference', $dc['palettes']['default']);

/**
 * Fields
 */
$fields = [
    'reference' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_cfg_tag']['reference'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => [
            'rgxp'     => 'natural',
            'tl_class' => 'w50',
        ],
        'sql'       => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
    ],
];

$dc['fields'] = array_merge($dc['fields'], $fields);

$dc['fields']['name']['sql']['length'] = 255;
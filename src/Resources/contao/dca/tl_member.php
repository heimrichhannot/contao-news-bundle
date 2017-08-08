<?php

$dc = &$GLOBALS['TL_DCA']['tl_member'];

/**
 * Palettes
 */
\Contao\CoreBundle\DataContainer\PaletteManipulator::create()->addField('title', 'personal_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)->addField(
        'organization',
        'personal_legend',
        \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND
    )->applyToPalette('default', 'tl_member');


/**
 * Fields
 */
$fields = [
    'title'        => [
        'label'     => &$GLOBALS['TL_LANG']['tl_member']['title'],
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'feEditable' => true, 'feViewable' => true, 'feGroup' => 'personal', 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'organization' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_member']['organization'],
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'feEditable' => true, 'feViewable' => true, 'feGroup' => 'personal', 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
];

$dc['fields'] = array_merge($dc['fields'], $fields);
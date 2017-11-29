<?php

/**
 * Extend default palette
 */
$GLOBALS['TL_DCA']['tl_user']['palettes']['extend'] = str_replace('newsfeeds', 'newslists,newslistp,newsfeeds', $GLOBALS['TL_DCA']['tl_user']['palettes']['extend']);
$GLOBALS['TL_DCA']['tl_user']['paettes']['custom'] = str_replace('newsfeeds', 'newslists,newslistp,newsfeeds', $GLOBALS['TL_DCA']['tl_user']['palettes']['custom']);


/**
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['newslists'] = [
    'label'      => &$GLOBALS['TL_LANG']['tl_user']['newslists'],
    'exclude'    => true,
    'inputType'  => 'checkbox',
    'foreignKey' => 'tl_news_list_archive.title',
    'eval'       => ['multiple' => true],
    'sql'        => "blob NULL",
];

$GLOBALS['TL_DCA']['tl_user']['fields']['newslistp'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['newslistp'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'options'   => ['create', 'delete'],
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval'      => ['multiple' => true],
    'sql'       => "blob NULL",
];
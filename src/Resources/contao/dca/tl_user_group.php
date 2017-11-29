<?php

/**
 * Extend default palette
 */
$GLOBALS['TL_DCA']['tl_user_group']['palettes']['default'] =
    str_replace('newsfeeds', 'newslists,newslistp,newsfeeds', $GLOBALS['TL_DCA']['tl_user_group']['palettes']['default']);


/**
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user_group']['fields']['newslists'] = [
    'label'      => &$GLOBALS['TL_LANG']['tl_user']['newslists'],
    'exclude'    => true,
    'inputType'  => 'checkbox',
    'foreignKey' => 'tl_news_list_archive.title',
    'eval'       => ['multiple' => true],
    'sql'        => "blob NULL",
];

$GLOBALS['TL_DCA']['tl_user_group']['fields']['newslistp'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['newslistp'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'options'   => ['create', 'delete'],
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval'      => ['multiple' => true],
    'sql'       => "blob NULL",
];
<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


$dc = &$GLOBALS['TL_DCA']['tl_news_feed'];

$dc['palettes']['default'] = str_replace('archives', 'archives,sources', $dc['palettes']['default']);

$fields = [
    'sources' => [
        'label'            => &$GLOBALS['TL_LANG']['tl_news_feed']['sources'],
        'exclude'          => true,
        'inputType'        => 'checkbox',
        'filter'           => true,
        'options_callback' => ['app.news_feed_generator','getDcaSourceOptions'],
        'eval'             => ['multiple' => true],
        'sql'              => "blob NULL"
    ]
];

$dc['fields'] = array_merge($dc['fields'], $fields);
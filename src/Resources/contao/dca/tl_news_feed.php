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


$dc['palettes']['default'] = str_replace('format', 'format,feedGeneration', $dc['palettes']['default']);


$fields = [
    'sources'        => [
        'label'            => &$GLOBALS['TL_LANG']['tl_news_feed']['sources'],
        'exclude'          => true,
        'inputType'        => 'checkbox',
        'filter'           => true,
        'options_callback' => ['app.news_feed_generator', 'getDcaSourceOptions'],
        'eval'             => ['multiple' => true],
        'sql'              => "blob NULL"
    ],
    'feedGeneration' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news_feed']['feedGeneration'],
        'default'   => \HeimrichHannot\NewsBundle\Component\NewsFeedGenerator::FEEDGENERATION_XML,
        'exclude'   => true,
        'filter'    => true,
        'inputType' => 'select',
        'options'   => [
            \HeimrichHannot\NewsBundle\Component\NewsFeedGenerator::FEEDGENERATION_XML => $GLOBALS['TL_LANG']['tl_news_feed']['feedGeneration_xml'],
            \HeimrichHannot\NewsBundle\Component\NewsFeedGenerator::FEEDGENERATION_DYNAMIC => $GLOBALS['TL_LANG']['tl_news_feed']['feedGeneration_dynamic'],
        ],
        'eval'      => ['tl_class' => 'w50'],
        'sql'       => "varchar(32) NOT NULL default '".\HeimrichHannot\NewsBundle\Component\NewsFeedGenerator::FEEDGENERATION_XML."'"
    ]
];

$dc['fields'] = array_merge($dc['fields'], $fields);
<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dc = &$GLOBALS['TL_DCA']['tl_news_archive'];

/**
 * Add a global operation to tl_news_archive
 */
array_insert($GLOBALS['TL_DCA']['tl_news_archive']['list']['global_operations'], 1, [
    'lists' => [
        'label'      => &$GLOBALS['TL_LANG']['tl_news_archive']['lists'],
        'href'       => 'table=tl_news_list_archive',
        'icon'       => 'folderC.svg',
        'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="c"',
    ],
]);

/**
 * Palettes
 */
$dc['palettes']['default'] = str_replace('allowComments', 'allowComments;{palettes_legend},addCustomNewsPalettes;', $dc['palettes']['default']);

/**
 * Selectors
 */
$dc['palettes']['__selector__'][] = 'addCustomNewsPalettes';

/**
 * Subpalettes
 */
$dc['subpalettes']['addCustomNewsPalettes'] = 'customNewsPalettes';

/**
 * Fields
 */
$fields = [
    'customNewsPalettes'    => [
        'label'            => &$GLOBALS['TL_LANG']['tl_news_archive']['customNewsPalettes'],
        'inputType'        => 'select',
        'options_callback' => ['HeimrichHannot\NewsBundle\Module', 'getNewsPalettes'],
        'eval'             => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
        'exclude'          => true,
        'sql'              => "varchar(50) NOT NULL default ''",
    ],
    'addCustomNewsPalettes' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['addCustomNewsPalettes'],
        'inputType' => 'checkbox',
        'exclude'   => true,
        'sql'       => "char(1) NOT NULL default ''",
        'eval'      => ['submitOnChange' => true],
    ],
];

$dc['fields'] = array_merge($dc['fields'], $fields);

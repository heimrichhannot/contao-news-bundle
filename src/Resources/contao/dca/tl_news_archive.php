<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dc = $GLOBALS['TL_DCA']['tl_news_archive'];

/**
 * Add a global operation to tl_news_archive
 */
array_insert($GLOBALS['TL_DCA']['tl_news_archive']['list']['global_operations'], 1, [
    'lists' => [
        'label'      => &$GLOBALS['TL_LANG']['tl_news_archive']['lists'],
        'href'       => 'table=tl_news_list_archive',
        'icon'       => 'folderC.svg',
        'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="c"'
    ]
]);
<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */
/**
 * Front end modules
 */
array_insert(
    $GLOBALS['FE_MOD'],
    2,
    [
        'news' => [
            'n_expert' => 'HeimrichHannot\NewsBundle\Module\ModuleExpert',
        ],
    ]
);
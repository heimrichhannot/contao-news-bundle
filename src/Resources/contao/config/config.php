<?php

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_news'] = '\HeimrichHannot\NewsBundle\NewsModel';


/**
 * Front end modules
 */
array_insert(
    $GLOBALS['FE_MOD'],
    2,
    [
        'news' => [
            'news_contact_box' => 'Dav\NewsBundle\Module\ModuleNewsContactBox',
        ],
    ]
);
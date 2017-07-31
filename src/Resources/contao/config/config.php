<?php

/**
 * Front end modules
 */
array_insert(
    $GLOBALS['FE_MOD'],
    2,
    [
        'news' => [
            'news_contact_box'    => 'HeimrichHannot\NewsBundle\Module\ModuleNewsContactBox',
            'news_readers_survey' => 'HeimrichHannot\NewsBundle\Module\ModuleNewsReadersSurvey',
        ],
    ]
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_news']      = '\HeimrichHannot\NewsBundle\NewsModel';
$GLOBALS['TL_MODELS']['tl_news_tags'] = '\HeimrichHannot\NewsBundle\NewsTagsModel';
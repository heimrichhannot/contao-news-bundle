<?php

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_news'] = '\HeimrichHannot\NewsBundle\NewsModel';
$GLOBALS['TL_MODELS']['tl_news_tags'] = '\HeimrichHannot\NewsBundle\NewsTagsModel';

$arrayNewsFeedGeneratorHook = array_search('News',array_column($GLOBALS['TL_HOOKS']['generateXmlFiles'], 0));
$GLOBALS['TL_HOOKS']['generateXmlFiles'][$arrayNewsFeedGeneratorHook][0] = \HeimrichHannot\NewsBundle\News::class;
//$GLOBALS['TL_HOOKS']['generateXmlFiles'][] = ['app.news_feed_generator','generateFeeds'];


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
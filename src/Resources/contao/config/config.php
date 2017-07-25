<?php

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_news'] = '\HeimrichHannot\NewsBundle\NewsModel';
$GLOBALS['TL_MODELS']['tl_news_tags'] = '\HeimrichHannot\NewsBundle\NewsTagsModel';

$GLOBALS['TL_HOOKS']['generateXmlFiles'][] = ['\HeimrichHannot\NewsBundle\NewsFeedGenerator','generateFeedsByTag'];

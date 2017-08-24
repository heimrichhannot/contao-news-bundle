<?php

/**
 * Front end modules
 */
array_insert(
    $GLOBALS['FE_MOD'],
    2,
    [
        'news' => [
            'news_contact_box'           => 'HeimrichHannot\NewsBundle\Module\ModuleNewsContactBox',
            'news_readers_survey'        => 'HeimrichHannot\NewsBundle\Module\ModuleNewsReadersSurvey',
            'news_readers_survey_result' => 'HeimrichHannot\NewsBundle\Module\ModuleNewsReadersSurveyResult',
            'news_info_box'              => 'HeimrichHannot\NewsBundle\Module\ModuleNewsInfoBox',
            'newslist_related'           => 'HeimrichHannot\NewsBundle\Module\ModuleNewsListRelated',
        ],
    ]
);

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['content']['news']['tables'][] = 'tl_news_list';

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_news'] = '\HeimrichHannot\NewsBundle\NewsModel';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['initializeSystem']['hh-news-bundle']   = ['HeimrichHannot\NewsBundle\Controller\FrontendController', 'xhrAction'];
$GLOBALS['TL_HOOKS']['parseArticles']['hh-news-bundle']      = ['HeimrichHannot\NewsBundle\Hooks', 'parseArticleHook'];
$GLOBALS['TL_HOOKS']['newsListCountItems']['hh-news-bundle'] = ['HeimrichHannot\NewsBundle\Hooks', 'newsListCountItemsHook'];
$GLOBALS['TL_HOOKS']['newsListFetchItems']['hh-news-bundle'] = ['HeimrichHannot\NewsBundle\Hooks', 'newsListFetchItemsHook'];
$GLOBALS['TL_HOOKS']['getPageLayout']['hh-news-bundle']      = ['HeimrichHannot\NewsBundle\Hooks', 'getPageLayoutHook'];
/**
 * Ajax Actions
 */
$GLOBALS['AJAX'][\HeimrichHannot\NewsBundle\News::XHR_GROUP] = [
    'actions' => [
        \HeimrichHannot\NewsBundle\News::XHR_READER_SURVEY_RESULT_ACTION => [
            'arguments' => [
                \HeimrichHannot\NewsBundle\News::XHR_PARAMETER_ID,
                \HeimrichHannot\NewsBundle\News::XHR_PARAMETER_ITEMS,
            ],
            'optional'  => [],
        ],
    ],
];
<?php

/**
 * Front end modules
 */
array_insert($GLOBALS['FE_MOD'], 2, [
    'news' => [
        'news_contact_box'           => 'HeimrichHannot\NewsBundle\Module\ModuleNewsContactBox',
        'news_readers_survey'        => 'HeimrichHannot\NewsBundle\Module\ModuleNewsReadersSurvey',
        'news_readers_survey_result' => 'HeimrichHannot\NewsBundle\Module\ModuleNewsReadersSurveyResult',
        'news_info_box'              => 'HeimrichHannot\NewsBundle\Module\ModuleNewsInfoBox',
        'newslist_related'           => 'HeimrichHannot\NewsBundle\Module\ModuleNewsListRelated',
    ],
]);

$GLOBALS['FE_MOD']['news']['newsreader'] = 'HeimrichHannot\NewsBundle\Module\ModuleNewsReader';

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['content']['news']['tables'][]  = 'tl_news_list';
$GLOBALS['BE_MOD']['content']['news']['tables'][] = 'tl_news_list_archive';

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_news_list']         = '\HeimrichHannot\NewsBundle\Model\NewsListModel';
$GLOBALS['TL_MODELS']['tl_news_list_archive'] = '\HeimrichHannot\NewsBundle\Model\NewsListArchiveModel';
$GLOBALS['TL_MODELS']['tl_news_tags']         = '\HeimrichHannot\NewsBundle\Model\NewsTagsModel';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['initializeSystem']['heimrichhannot_news']   = ['HeimrichHannot\NewsBundle\Controller\FrontendController', 'xhrAction'];
$GLOBALS['TL_HOOKS']['parseArticles']['heimrichhannot_news']      = ['heimrichhannot_news.listener.hooks', 'parseArticles'];
$GLOBALS['TL_HOOKS']['newsListCountItems']['heimrichhannot_news'] = ['heimrichhannot_news.listener.hooks', 'newsListCountItems'];
$GLOBALS['TL_HOOKS']['newsListFetchItems']['heimrichhannot_news'] = ['heimrichhannot_news.listener.hooks', 'newsListFetchItems'];
$GLOBALS['TL_HOOKS']['getPageLayout']['heimrichhannot_news']      = ['heimrichhannot_news.listener.hooks', 'getPageLayout'];
$GLOBALS['TL_HOOKS']['replaceInsertTags']['heimrichhannot_news']  = ['heimrichhannot_news.listener.insert_tags', 'onReplaceInsertTags'];
$GLOBALS['TL_HOOKS']['getSearchablePages']['heimrichhannot_news'] = ['heimrichhannot_news.listener.searchable_pages', 'getSearchablePages'];

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

/**
 * Permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'newslists';
$GLOBALS['TL_PERMISSIONS'][] = 'newslistp';
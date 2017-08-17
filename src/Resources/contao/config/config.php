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
$GLOBALS['TL_MODELS']['tl_news']      = '\HeimrichHannot\NewsBundle\NewsModel';
$GLOBALS['TL_MODELS']['tl_news_tags'] = '\HeimrichHannot\NewsBundle\NewsTagsModel';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['initializeSystem'][] = ['HeimrichHannot\NewsBundle\Controller\FrontendController', 'xhrAction'];
$GLOBALS['TL_HOOKS']['parseArticles'][]    = ['HeimrichHannot\NewsBundle\Hooks', 'parseArticleHook'];

/**
 * Ajax Actions
 */
$GLOBALS['AJAX'][\HeimrichHannot\NewsBundle\News::XHR_GROUP] = [
    'actions' => [
        \HeimrichHannot\NewsBundle\News::XHR_READER_SURVEY_SAVE_ACTION   => [
            'arguments' => [
                \HeimrichHannot\NewsBundle\News::XHR_PARAMETER_ID,
                \HeimrichHannot\NewsBundle\News::INPUTS_ANSWER_ID,
            ],
            'optional'  => [],
        ],
        \HeimrichHannot\NewsBundle\News::XHR_READER_SURVEY_RESULT_ACTION => [
            'arguments' => [
                \HeimrichHannot\NewsBundle\News::XHR_PARAMETER_ID,
                \HeimrichHannot\NewsBundle\News::INPUTS_ANSWER_ID,
            ],
            'optional'  => [],
        ],
    ],
];
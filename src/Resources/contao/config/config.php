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
 * Models
 */
$GLOBALS['TL_MODELS']['tl_news']      = '\HeimrichHannot\NewsBundle\NewsModel';
$GLOBALS['TL_MODELS']['tl_news_tags'] = '\HeimrichHannot\NewsBundle\NewsTagsModel';

$GLOBALS['TL_HOOKS']['initializeSystem'][] = ['HeimrichHannot\NewsBundle\Controller\FrontendController', 'xhrAction'];

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
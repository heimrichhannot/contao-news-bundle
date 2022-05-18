<?php

$bundleClass = new \HeimrichHannot\NewsBundle\HeimrichHannotContaoNewsBundle;

/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['content']['news']['tables'][] = 'tl_news_list';
$GLOBALS['BE_MOD']['content']['news']['tables'][] = 'tl_news_list_archive';

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_news_list']         = '\HeimrichHannot\NewsBundle\Model\NewsListModel';
$GLOBALS['TL_MODELS']['tl_news_list_archive'] = '\HeimrichHannot\NewsBundle\Model\NewsListArchiveModel';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags']['heimrichhannot_news']  = ['huh.news.listener.insert_tags', 'onReplaceInsertTags'];
$GLOBALS['TL_HOOKS']['getSearchablePages']['heimrichhannot_news'] = ['huh.news.listener.searchable_pages', 'getSearchableNewsListPages'];

/**
 * Permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'newslists';
$GLOBALS['TL_PERMISSIONS'][] = 'newslistp';

/**
 * Modal module configuration
 */
$GLOBALS['MODAL_MODULES']['newslist'] = [
    'invokePalette' => 'customTpl;', // The modal palette will be invoked after the field customTpl; as example
];

/**
 * JS
 */
if (\Contao\System::getContainer()->get('huh.utils.container')->isBackend()) {
    $GLOBALS['TL_JAVASCRIPT']['be.news-bundle'] = 'bundles/heimrichhannotcontaonews/js/be-news-bundle.js';
}
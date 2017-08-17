<?php

/**
 * news_categories extension for Contao Open Source CMS
 *
 * Copyright (C) 2011-2014 Codefog
 *
 * @package news_categories
 * @author  Webcontext <http://webcontext.com>
 * @author  Codefog <info@codefog.pl>
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */

/**
 * Load tl_news_archive language file
 */
\System::loadLanguageFile('tl_news_archive');

/**
 * Table tl_news_list
 */
$GLOBALS['TL_DCA']['tl_news_list'] = [

    // Config
    'config'      => [
        'label'            => $GLOBALS['TL_LANG']['tl_news_archive']['lists'][0],
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'onload_callback'  => [//            array('tl_news_list', 'checkPermission')
        ],
        'sql'              => [
            'keys' => [
                'id'  => 'primary',
                'pid' => 'index',
            ],
        ],
        'backlink'         => 'do=news',
    ],

    // List
    'list'        => [
        'sorting'           => [
            'mode'        => 2,
            'fields'      => ['title'],
            'icon'        => 'system/modules/news_categories/assets/icon.png',
            //            'paste_button_callback' => ['tl_news_list', 'pasteCategory'],
            'panelLayout' => 'search',
        ],
        'label'             => [
            'fields' => ['title', 'frontendTitle'],
            'format' => '%s <span style="padding-left:3px;color:#b3b3b3;">[%s]</span>',
            //            'label_callback' => ['tl_news_list', 'generateLabel'],
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_news_list']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_news_list']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_news_list']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_news_list']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
            'toggle' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_news_list']['toggle'],
                'icon'       => 'visible.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                //                'button_callback' => ['tl_news_list', 'toggleIcon'],
            ],
        ],
    ],

    // Palettes
    'palettes'    => [
        '__selector__' => ['published'],
        'default'      => '{title_legend},title;{publish_legend},published',
    ],
    // Sub palettes
    'subpalettes' => [
        'published' => 'start,stop',
    ],
    // Fields
    'fields'      => [
        'id'        => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'pid'       => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'sorting'   => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp'    => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_news_list']['title'],
            'exclude'   => true,
            'search'    => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'news'      => [

        ],
        'published' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_news_list']['published'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'start'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_news_list']['start'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''",
        ],
        'stop'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_news_list']['stop'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''",
        ],
    ],
];
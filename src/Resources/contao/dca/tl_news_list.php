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
        'label'             => $GLOBALS['TL_LANG']['tl_news_archive']['lists'][0],
        'dataContainer'     => 'Table',
        'ptable'            => 'tl_news_list_archive',
        'enableVersioning'  => true,
        'onload_callback'   => [
            ['HeimrichHannot\NewsBundle\Backend\NewsList', 'checkPermission'],
        ],
        'onsubmit_callback' => [
            ['HeimrichHannot\Haste\Dca\General', 'setDateAdded'],
        ],
        'oncopy_callback'   => [
            ['HeimrichHannot\FieldPalette\FieldPalette', 'copyFieldPaletteRecords'],
        ],
        'sql'               => [
            'keys' => [
                'id'  => 'primary',
                'pid' => 'index',
            ],
        ],
        'backlink'          => 'do=news',
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
                'icon'  => 'edit.svg',
            ],
            'copy'   => [
                'label'           => &$GLOBALS['TL_LANG']['tl_news_list']['copy'],
                'href'            => 'act=copy',
                'icon'            => 'copy.svg',
                'button_callback' => ['HeimrichHannot\NewsBundle\Backend\NewsList', 'copyList'],
            ],
            'delete' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_news_list']['delete'],
                'href'            => 'act=delete',
                'icon'            => 'delete.svg',
                'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
                'button_callback' => ['HeimrichHannot\NewsBundle\Backend\NewsList', 'deleteList'],
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_news_list']['toggle'],
                'icon'            => 'visible.svg',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => ['HeimrichHannot\NewsBundle\Backend\NewsList', 'toggleIcon'],
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_news_list']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.svg',
            ],
        ],
    ],

    // Palettes
    'palettes'    => [
        '__selector__' => ['published'],
        'default'      => '{general_legend},title,alias,news;{publish_legend},published',
    ],
    // Sub palettes
    'subpalettes' => [
        'published' => 'start,stop',
    ],
    // Fields
    'fields'      => [
        'id'            => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'pid'           => [
            'foreignKey' => 'tl_news_list_archive.title',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
            'relation'   => ['type' => 'belongsTo', 'load' => 'eager'],
        ],
        'sorting'       => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'tstamp'        => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'dateAdded'     => [
            'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting' => true,
            'flag'    => 6,
            'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
            'sql'     => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_news_list']['title'],
            'exclude'   => true,
            'search'    => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'alias'         => \HeimrichHannot\Haste\Dca\General::getAliasField(['HeimrichHannot\NewsBundle\Backend\NewsList', 'generateAlias']),
        'news'          => [
            'label'        => &$GLOBALS['TL_LANG']['tl_news_list']['news'],
            'inputType'    => 'fieldpalette',
            'foreignKey'   => 'tl_fieldpalette.id',
            'relation'     => ['type' => 'hasMany', 'load' => 'eager'],
            'eval'         => ['tl_class' => 'clr wizard'],
            'sql'          => "blob NULL",
            'fieldpalette' => [
                'config'   => [
                    'hidePublished' => false,
                ],
                'list'     => [
                    'label' => [
                        'fields'         => ['news_list_news'],
                        'format'         => '%s',
                        'label_callback' => ['HeimrichHannot\NewsBundle\Backend\NewsList', 'generateNewsItemLabel'],
                    ],
                ],
                'palettes' => [
                    'default' => 'news_list_news',
                ],
                'fields'   => [
                    'news_list_news' => [
                        'label'            => &$GLOBALS['TL_LANG']['tl_news_list']['news_list_news'],
                        'exclude'          => true,
                        'inputType'        => 'select',
                        'foreignKey'       => 'tl_news.title',
                        'relation'         => ['type' => 'hasMany', 'load' => 'eager'],
                        'options_callback' => ['HeimrichHannot\NewsBundle\Backend\NewsList', 'getNewsOptions'],
                        'eval'             => ['mandatory' => true, 'includeBlankOption' => true, 'chosen' => true],
                        'sql'              => "int(10) unsigned NOT NULL default '0'",
                    ],
                ],
            ],
        ],
        'published'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_news_list']['published'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'start'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_news_list']['start'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''",
        ],
        'stop'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_news_list']['stop'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''",
        ],
    ],
];

\HeimrichHannot\Haste\Dca\General::addAliasButton('tl_news_list');
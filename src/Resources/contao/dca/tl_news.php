<?php

/**
 * Load tl_content language file
 */
System::loadLanguageFile('tl_content');

$dc = &$GLOBALS['TL_DCA']['tl_news'];

/**
 * Selectors
 */
$dc['palettes']['__selector__'][] = 'addContactBox';
$dc['palettes']['__selector__'][] = 'add_teaser_image';
$dc['palettes']['__selector__'][] = 'teaser_overwriteMeta';
$dc['palettes']['__selector__'][] = 'add_readers_survey';
$dc['palettes']['__selector__'][] = 'infoBox';
$dc['palettes']['__selector__'][] = 'add_related_news';
$dc['palettes']['__selector__'][] = 'player';
$dc['palettes']['__selector__'][] = 'relocate';


/**
 * Palettes
 */
$dc['palettes']['default'] = str_replace('author;', 'author;{writers_legend:hide},writers;', $dc['palettes']['default']);
$dc['palettes']['default'] = str_replace(
    '{date_legend}',
    '{tags_legend:hide},tags;{related_news_legend:hide},add_related_news;{contact_box_legend},addContactBox;{info_box_legend:hide},infoBox;{readers_survey_legend:hide},add_readers_survey;{date_legend}',
    $dc['palettes']['default']
);
$dc['palettes']['default'] = str_replace('teaser;', 'teaser,teaser_short,add_teaser_image;{copyright_legend},copyright;', $dc['palettes']['default']);
$dc['palettes']['default'] = str_replace('source;', 'source;{meta_legend:hide},pageTitle,metaDescription,metaKeywords;{twitter_legend},twitterCard,twitterCreator;', $dc['palettes']['default']);
$dc['palettes']['default'] = str_replace('{image_legend}', '{player_legend},player;{image_legend}', $dc['palettes']['default']);
$dc['palettes']['default'] = str_replace('cssClass,', 'relocate,cssClass,', $dc['palettes']['default']);

/**
 * Subpalettes
 */
$dc['subpalettes']['addContactBox']        = 'contactBox_members,contactBox_header,contactBox_links';
$dc['subpalettes']['add_teaser_image']     = 'teaser_singleSRC,teaser_size,teaser_floating,teaser_imagemargin,teaser_fullsize,teaser_overwriteMeta';
$dc['subpalettes']['teaser_overwriteMeta'] = 'teaser_alt,teaser_imageTitle,teaser_imageUrl,teaser_caption';
$dc['subpalettes']['add_readers_survey']   = 'readers_survey';
$dc['subpalettes']['infoBox_text']         = 'infoBox_header, infoBox_text, infoBox_link, infoBox_linkText';
$dc['subpalettes']['add_related_news']     = 'related_news';
$dc['subpalettes']['player_internal']      = 'playerSRC,posterSRC';
$dc['subpalettes']['player_external']      = 'playerUrl,posterSRC';
$dc['subpalettes']['relocate_deindex']     = 'relocateUrl';
$dc['subpalettes']['relocate_redirect']    = 'relocateUrl';

/**
 * Fields
 */
$fields = [
    'teaser_short'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['teaser_short'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'textarea',
        'eval'      => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
        'sql'       => "text NULL",
    ],
    'tags'                       => [
        'label'         => &$GLOBALS['TL_LANG']['tl_news']['tags'],
        'exclude'       => true,
        'inputType'     => 'cfgTags',
        'eval'          => [
            'tagsManager' => 'app.news', // Manager, required
            'tagsCreate'  => true, // Allow to create tags, optional (true by default)
            'tl_class'    => 'clr',
        ],
        'save_callback' => [['huh.news.listener.tag_manager', 'onFieldSave']],
        'relation'      => [
            'relationTable' => 'tl_news_tags',
        ],
        'sql'           => "blob NULL",
    ],
    'addContactBox'              => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['addContactBox'],
        'inputType' => 'checkbox',
        'exclude'   => true,
        'sql'       => "varchar(255) NOT NULL default ''",
        'eval'      => ['submitOnChange' => true],
    ],
    'contactBox_members'         => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contactBox_members'],
        'inputType' => 'tagsinput',
        'sql'       => "blob NULL",
        'eval'      => [
            'placeholder' => &$GLOBALS['TL_LANG']['tl_news']['placeholders']['contactBox_members'],
            'freeInput'   => false,
            'multiple'    => true,
            'mode'        => \TagsInput::MODE_REMOTE,
            'remote'      => [
                'fields'       => ['email', 'firstname', 'lastname', 'id'],
                'format'       => '%s (%s %s) [ID:%s]',
                'queryField'   => 'email',
                'queryPattern' => '%QUERY%',
                'foreignKey'   => 'tl_member.id',
                'limit'        => 10,
            ],
        ],
    ],
    'contactBox_header'          => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contactBox_header'],
        'inputType' => 'text',
        'sql'       => "varchar(255) NOT NULL default ''",
        'eval'      => ['mandatory' => true],
    ],
    'contactBox_links'           => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contactBox_links'],
        'inputType' => 'multiColumnEditor',
        'eval'      => [
            'multiColumnEditor' => [
                'class'               => 'contact_box_link',
                // set to 0 if it should also be possible to have *no* row (default: 1)
                'minRowCount'         => 0,
                // defaults to false
                'skipCopyValuesOnAdd' => false,
                'fields'              => [
                    // place your fields here as you would normally in your DCA
                    // (sql is not required)
                    'link' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contactBox_link'],
                        'inputType' => 'text',
                        'eval'      => ['groupStyle' => 'width:250px', 'rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'tl_class' => 'w50 wizard'],
                    ],
                    'text' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contactBox_linkText'],
                        'inputType' => 'text',
                        'eval'      => ['groupStyle' => 'width:250px'],
                    ],
                ],
            ],
        ],
        'sql'       => "blob NULL",
    ],
    'writers'                    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['writers'],
        'inputType' => 'tagsinput',
        'sql'       => "blob NULL",
        'eval'      => [
            'placeholder' => &$GLOBALS['TL_LANG']['tl_news']['placeholders']['writers'],
            'freeInput'   => false,
            'multiple'    => true,
            'mode'        => \TagsInput::MODE_REMOTE,
            'remote'      => [
                'fields'       => ['firstname', 'lastname', 'id'],
                'format'       => '%s %s [ID:%s]',
                'queryField'   => 'lastname',
                'queryPattern' => '%QUERY%',
                'foreignKey'   => 'tl_member.id',
                'limit'        => 10,
            ],
        ],
    ],
    'add_teaser_image'           => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['add_teaser_image'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'teaser_overwriteMeta'       => [
        'label'     => &$GLOBALS['TL_LANG']['tl_content']['overwriteMeta'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'teaser_singleSRC'           => [
        'label'     => &$GLOBALS['TL_LANG']['tl_content']['singleSRC'],
        'exclude'   => true,
        'inputType' => 'fileTree',
        'eval'      => ['filesOnly' => true, 'extensions' => Config::get('validImageTypes'), 'fieldType' => 'radio', 'mandatory' => true],
        'sql'       => "binary(16) NULL",
    ],
    'teaser_alt'                 => [
        'label'     => &$GLOBALS['TL_LANG']['tl_content']['alt'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'teaser_imageTitle'          => [
        'label'     => &$GLOBALS['TL_LANG']['tl_content']['imageTitle'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'teaser_size'                => [
        'label'            => &$GLOBALS['TL_LANG']['tl_content']['size'],
        'exclude'          => true,
        'inputType'        => 'imageSize',
        'reference'        => &$GLOBALS['TL_LANG']['MSC'],
        'eval'             => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
        'options_callback' => function () {
            return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
        },
        'sql'              => "varchar(64) NOT NULL default ''",
    ],
    'teaser_imagemargin'         => [
        'label'     => &$GLOBALS['TL_LANG']['tl_content']['imagemargin'],
        'exclude'   => true,
        'inputType' => 'trbl',
        'options'   => $GLOBALS['TL_CSS_UNITS'],
        'eval'      => ['includeBlankOption' => true, 'tl_class' => 'w50'],
        'sql'       => "varchar(128) NOT NULL default ''",
    ],
    'teaser_imageUrl'            => [
        'label'     => &$GLOBALS['TL_LANG']['tl_content']['imageUrl'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => [
            'rgxp'           => 'url',
            'decodeEntities' => true,
            'maxlength'      => 255,
            'dcaPicker'      => true,
            'fieldType'      => 'radio',
            'filesOnly'      => true,
            'tl_class'       => 'w50 wizard',
        ],
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'teaser_fullsize'            => [
        'label'     => &$GLOBALS['TL_LANG']['tl_content']['fullsize'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50 m12'],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'teaser_caption'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_content']['caption'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'allowHtml' => true, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'teaser_floating'            => [
        'label'     => &$GLOBALS['TL_LANG']['tl_content']['floating'],
        'default'   => 'above',
        'exclude'   => true,
        'inputType' => 'radioTable',
        'options'   => ['above', 'left', 'right', 'below'],
        'eval'      => ['cols' => 4, 'tl_class' => 'w50'],
        'reference' => &$GLOBALS['TL_LANG']['MSC'],
        'sql'       => "varchar(12) NOT NULL default ''",
    ],
    'add_readers_survey'         => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['add_readers_survey'],
        'inputType' => 'checkbox',
        'exclude'   => true,
        'sql'       => "char(1) NOT NULL default ''",
        'eval'      => ['submitOnChange' => true],
    ],
    'readers_survey'             => [
        'label'        => &$GLOBALS['TL_LANG']['tl_news']['readers_survey_question'],
        'inputType'    => 'fieldpalette',
        'exclude'      => true,
        'foreignKey'   => 'tl_fieldpalette.id',
        'relation'     => ['type' => 'hasMany', 'load' => 'eager'],
        'sql'          => "blob NULL",
        'fieldpalette' => [
            'config'   => [
                'hidePublished' => false,
            ],
            'list'     => [
                'label' => [
                    'fields' => ['news_question'],
                    'format' => '%s',
                ],
            ],
            'palettes' => [
                'default' => 'news_question, news_answers',
            ],
            'fields'   => [
                'news_question' => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_news']['readers_survey_question'],
                    'exclude'   => true,
                    'search'    => true,
                    'inputType' => 'text',
                    'eval'      => ['maxlength' => 255, 'tl_class' => 'long'],
                    'sql'       => "varchar(255) NOT NULL default ''",
                ],
                'news_answers'  => [
                    'label'        => &$GLOBALS['TL_LANG']['tl_news']['news_answers'],
                    'inputType'    => 'fieldpalette',
                    'foreignKey'   => 'tl_fieldpalette.id',
                    'relation'     => ['type' => 'hasMany', 'load' => 'eager'],
                    'sql'          => "blob NULL",
                    'fieldpalette' => [
                        'config'   => [
                            'hidePublished' => false,
                        ],
                        'list'     => [
                            'label' => [
                                'fields' => ['news_answer'],
                                'format' => '%s',
                            ],
                        ],
                        'palettes' => [
                            'default' => 'news_answer',
                        ],
                        'fields'   => [
                            'news_answer'      => [
                                'label'     => &$GLOBALS['TL_LANG']['tl_news']['news_answers'],
                                'exclude'   => true,
                                'search'    => true,
                                'inputType' => 'text',
                                'eval'      => ['maxlength' => 255, 'tl_class' => 'long'],
                                'sql'       => "varchar(255) NOT NULL default ''",
                            ],
                            'news_answer_vote' => [
                                'label'     => &$GLOBALS['TL_LANG']['tl_news']['readers_survey_answers'],
                                'exclude'   => true,
                                'search'    => true,
                                'inputType' => 'text',
                                'eval'      => ['maxlength' => 255, 'tl_class' => 'long'],
                                'sql'       => "int(10) NOT NULL default 0",
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'facebook_counter'           => [
        'sql' => ['type' => 'integer', 'default' => '0'],
    ],
    'facebook_updated_at'        => [
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ],
    'twitter_counter'            => [
        'sql' => ['type' => 'integer', 'default' => '0'],
    ],
    'twitter_updated_at'         => [
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ],
    'google_plus_counter'        => [
        'sql' => ['type' => 'integer', 'default' => '0'],
    ],
    'google_plus_updated_at'     => [
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ],
    'disqus_counter'             => [
        'sql' => ['type' => 'integer', 'default' => '0'],
    ],
    'disqus_updated_at'          => [
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ],
    'google_analytic_counter'    => [
        'sql' => ['type' => 'integer', 'default' => '0'],
    ],
    'google_analytic_updated_at' => [
        'sql' => "int(10) unsigned NOT NULL default '0'",
    ],
    'infoBox'                    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['infoBox'],
        'inputType' => 'radio',
        'options'   => ['none', 'text'],
        'reference' => &$GLOBALS['TL_LANG']['tl_news']['reference']['infoBox'],
        'default'   => 'none',
        'exclude'   => true,
        'filter'    => true,
        'sql'       => "varchar(20) NOT NULL default ''",
        'eval'      => ['submitOnChange' => true],
    ],
    'infoBox_header'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['infoBox_header'],
        'inputType' => 'text',
        'exclude'   => true,
        'eval'      => ['mandatory' => true],
        'sql'       => "varchar(255) NOT NULL DEFAULT ''",
    ],
    'infoBox_text'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['infoBox_text'],
        'inputType' => 'textarea',
        'exclude'   => true,
        'eval'      => ['rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => true],
        'sql'       => "text NULL",
    ],
    'infoBox_link'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['infoBox_link'],
        'inputType' => 'text',
        'exclude'   => true,
        'eval'      => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'tl_class' => 'w50 wizard'],
        'sql'       => "varchar(255) NOT NULL DEFAULT ''",
    ],
    'infoBox_linkText'           => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['infoBox_linkText'],
        'inputType' => 'text',
        'exclude'   => true,
        'sql'       => "varchar(255) NOT NULL DEFAULT ''",
    ],
    'add_related_news'           => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['add_related_news'],
        'inputType' => 'checkbox',
        'exclude'   => true,
        'sql'       => "char(1) NOT NULL default ''",
        'eval'      => ['submitOnChange' => true],
    ],
    'related_news'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['related_news'],
        'inputType' => 'tagsinput',
        'sql'       => "blob NULL",
        'eval'      => [
            'placeholder'   => &$GLOBALS['TL_LANG']['tl_news']['placeholders']['related_news'],
            'freeInput'     => false,
            'multiple'      => true,
            'mode'          => \TagsInput::MODE_REMOTE,
            'tags_callback' => [['huh.news.backend.tl_news', 'getRelatedNews']],
            'remote'        => [
                'fields'       => ['headline', 'id'],
                'format'       => '%s [ID:%s]',
                'queryField'   => 'headline',
                'queryPattern' => '%QUERY%',
                'foreignKey'   => 'tl_news.id',
                'limit'        => 10,
            ],
        ],
    ],
    'pageTitle'                  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['pageTitle'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'metaDescription'            => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['metaDescription'],
        'exclude'   => true,
        'inputType' => 'textarea',
        'eval'      => ['tl_class' => 'clr'],
        'sql'       => "text NULL",
    ],
    'metaKeywords'               => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['metaKeywords'],
        'inputType' => 'tagsinput',
        'eval'      => [
            'placeholder' => &$GLOBALS['TL_LANG']['tl_news']['placeholders']['metaKeywords'],
            'freeInput'   => true,
            'multiple'    => true,
        ],
        'sql'       => "blob NULL",
    ],
    'twitterCard'                => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['twitterCard'],
        'exclude'   => true,
        'inputType' => 'select',
        'default'   => 'summary_large_image',
        'reference' => &$GLOBALS['TL_LANG']['tl_news']['reference']['twitterCardTypes'],
        'options'   => ['summary', 'summary_large_image', 'player'],
        'eval'      => ['tl_class' => 'w50', 'includeBlankOption' => true],
        'sql'       => "varchar(24) NOT NULL default 'summary_large_image'",
    ],
    'twitterCreator'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['twitterCreator'],
        'exclude'   => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'player'                     => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['player'],
        'exclude'   => true,
        'filter'    => true,
        'inputType' => 'radio',
        'default'   => 'none',
        'options'   => ['none', 'internal', 'external'],
        'reference' => &$GLOBALS['TL_LANG']['tl_news']['reference']['player'],
        'eval'      => ['chosen' => true, 'submitOnChange' => true],
        'sql'       => "varchar(32) NOT NULL default ''"
    ],
    'playerSRC'                  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['playerSRC'],
        'exclude'   => true,
        'inputType' => 'fileTree',
        'eval'      => ['multiple' => true, 'fieldType' => 'checkbox', 'files' => true, 'mandatory' => true],
        'sql'       => "blob NULL"
    ],
    'playerUrl'                  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['playerUrl'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => ['mandatory' => false, 'decodeEntities' => true, 'maxlength' => 255],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'posterSRC'                  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['posterSRC'],
        'exclude'   => true,
        'inputType' => 'fileTree',
        'eval'      => ['filesOnly' => true, 'fieldType' => 'radio'],
        'sql'       => "binary(16) NULL"
    ],
    'copyright'                  => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['copyright'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => ['maxlength' => 255, 'tl_class' => 'long'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ],
    'relocate'                   => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['relocate'],
        'inputType' => 'radio',
        'options'   => ['none', 'deindex', 'redirect'],
        'reference' => &$GLOBALS['TL_LANG']['tl_news']['reference']['relocate'],
        'exclude'   => true,
        'sql'       => "varchar(12) NOT NULL default 'none'",
        'eval'      => ['submitOnChange' => true],
    ],
    'relocateUrl'                => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['relocateUrl'],
        'exclude'   => true,
        'search'    => true,
        'inputType' => 'text',
        'eval'      => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'tl_class' => 'full wizard'],
        'sql'       => "varchar(255) NOT NULL default ''"
    ]
];

$dc['fields'] = array_merge($dc['fields'], $fields);
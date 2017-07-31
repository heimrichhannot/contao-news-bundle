<?php

/**
 * Load tl_content language file
 */
System::loadLanguageFile('tl_content');

$dc = &$GLOBALS['TL_DCA']['tl_news'];

/**
 * Selectors
 */
$dc['palettes']['__selector__'][] = 'add_contact_box';
$dc['palettes']['__selector__'][] = 'add_teaser_image';
$dc['palettes']['__selector__'][] = 'teaser_overwriteMeta';
$dc['palettes']['__selector__'][] = 'add_readers_survey';
$dc['palettes']['__selector__'][] = 'info_box_selector';

/**
 * Palettes
 */
$dc['palettes']['default'] = str_replace('author;', 'author;{writers_legend:hide},writers;', $dc['palettes']['default']);
$dc['palettes']['default'] = str_replace(
    '{date_legend}',
    '{tags_legend:hide},tags;{contact_box_legend},add_contact_box;{info_box_legend:hide},info_box_selector;{readers_survey_legend:hide},add_readers_survey;{date_legend}',
    $dc['palettes']['default']
);
$dc['palettes']['default'] = str_replace('teaser;', 'teaser,add_teaser_image;', $dc['palettes']['default']);


/**
 * Subpalettes
 */
$dc['subpalettes']['add_contact_box']                 = 'contact_box_members,contact_box_header,contact_box_topic,contact_box_title,add_contact_box_link';
$dc['subpalettes']['add_teaser_image']                = 'teaser_singleSRC,teaser_size,teaser_floating,teaser_imagemargin,teaser_fullsize,teaser_overwriteMeta';
$dc['subpalettes']['teaser_overwriteMeta']            = 'teaser_alt,teaser_imageTitle,teaser_imageUrl,teaser_caption';
$dc['subpalettes']['add_readers_survey']              = 'readers_survey_question, readers_survey_answers';
$dc['subpalettes']['info_box_selector_info_box_text'] = 'info_box_text_header, info_box_text_text, info_box_text_link, info_box_text_link_text';
$dc['subpalettes']['info_box_selector_info_box_link'] = 'info_box_link';
$dc['subpalettes']['info_box_selector_info_box_none'] = '';


/**
 * Fields
 */
$fields = [
    'tags'                       => [
        'label'         => &$GLOBALS['TL_LANG']['tl_news']['tags'],
        'exclude'       => true,
        'inputType'     => 'cfgTags',
        'eval'          => [
            'tagsManager' => 'app.news', // Manager, required
            'tagsCreate'  => true, // Allow to create tags, optional (true by default)
            'tl_class'    => 'clr',
        ],
        'save_callback' => [['heimrichhannot_news.listener.tag_manager', 'onFieldSave']],
        'relation'      => [
            'relationTable' => 'tl_news_tags',
        ],
        'sql'           => "blob NULL",
    ],
    'add_contact_box'            => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['add_contact_box'],
        'inputType' => 'checkbox',
        'exclude'   => true,
        'sql'       => "varchar(255) NOT NULL default ''",
        'eval'      => ['submitOnChange' => true],
    ],
    'contact_box_members'        => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contact_box_members'],
        'inputType' => 'tagsinput',
        'sql'       => "blob NULL",
        'eval'      => [
            'placeholder' => &$GLOBALS['TL_LANG']['tl_news']['placeholders']['contact_box_members'],
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
    'contact_box_header'         => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contact_box_header'],
        'inputType' => 'text',
        'sql'       => "varchar(255) NOT NULL default ''",
        'eval'      => ['mandatory' => true],
    ],
    'contact_box_topic'          => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contact_box_topic'],
        'inputType' => 'text',
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'contact_box_title'          => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contact_box_title'],
        'inputType' => 'text',
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'add_contact_box_link'       => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['add_contact_box_link'],
        'inputType' => 'multiColumnEditor',
        'eval'      => [
            'multiColumnEditor' => [
                'class'               => 'contact_box_link',
                // set to 0 if it should also be possible to have *no* row (default: 1)
                'minRowCount'         => 0,
                // set to 0 if an infinite number of rows should be possible (default: 0)
                'maxRowCount'         => 0,
                // defaults to false
                'skipCopyValuesOnAdd' => false,
                'fields'              => [
                    // place your fields here as you would normally in your DCA
                    // (sql is not required)
                    'contact_box_link'      => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contact_box_link'],
                        'inputType' => 'text',
                        'eval'      => ['groupStyle' => 'width:150px'],
                    ],
                    'contact_box_link_text' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contact_box_link_text'],
                        'inputType' => 'text',
                        'eval'      => ['groupStyle' => 'width:150px'],
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
        'options_callback' => function ()
        {
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
        'sql'       => "varchar(255) NOT NULL default ''",
        'eval'      => ['submitOnChange' => true],
    ],
    'readers_survey_question'    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['readers_survey_question'],
        'inputType' => 'text',
        'sql'       => "varchar(255) NOT NULL DEFAULT ''",
        'eval'      => ['mandatory' => true],
    ],
    'readers_survey_answers'     => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['readers_survey_answers'],
        'inputType' => 'multiColumnEditor',
        'eval'      => [
            'multiColumnEditor' => [
                'class'               => 'readers_survey_answers',
                // set to 0 if it should also be possible to have *no* row (default: 1)
                'minRowCount'         => 1,
                // set to 0 if an infinite number of rows should be possible (default: 0)
                'maxRowCount'         => 0,
                // defaults to false
                'skipCopyValuesOnAdd' => false,
                'fields'              => [
                    // place your fields here as you would normally in your DCA
                    // (sql is not required)
                    'readers_survey_answer'      => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_news']['readers_survey_answer'],
                        'inputType' => 'text',
                        'eval'      => ['groupStyle' => 'width:800px', 'mandatory' => true],
                    ],
                    'readers_survey_answer_vote' => [],
                ],
            ],
        ],
        'sql'       => "blob NULL",
    ],
    'type'                       => [
        'exclude' => 0,
        'type'    => 'text',
        'eval'    => ['size' => 30],
        'sql'     => ['type' => 'string', 'default' => ''],
    ],
    'hidden'                     => [
        'exclude' => 1,
        'type'    => 'checkbox',
        'sql'     => 'int(1) NOT NULL default 0',
    ],
    'facebook_counter'           => [
        'exclude'   => 0,
        'inputType' => 'text',
        'eval'      => [
            'size' => 4,
            'rgxp' => 'alnum',
        ],
        'sql'       => ['type' => 'integer', 'default' => '0'],
    ],
    'facebook_updated_at'        => [
        'exclude'   => 0,
        'inputType' => 'text',
        'eval'      => [
            'size' => 10,
            'rgxp' => 'datim',
        ],
        'sql'       => "int(10) unsigned NOT NULL default '0'",
    ],
    'twitter_counter'            => [
        'exclude'   => 0,
        'inputType' => 'text',
        'eval'      => [
            'size' => 4,
            'rgxp' => 'alnum',
        ],
        'sql'       => ['type' => 'integer', 'default' => '0'],
    ],
    'twitter_updated_at'         => [
        'exclude'   => 0,
        'inputType' => 'text',
        'eval'      => [
            'size' => 10,
            'rgxp' => 'datim',
        ],
        'sql'       => "int(10) unsigned NOT NULL default '0'",
    ],
    'google_plus_counter'        => [
        'exclude'   => 0,
        'inputType' => 'text',
        'eval'      => [
            'size' => 4,
            'rgxp' => 'alnum',
        ],
        'sql'       => ['type' => 'integer', 'default' => '0'],
    ],
    'google_plus_updated_at'     => [
        'exclude'   => 0,
        'inputType' => 'text',
        'eval'      => [
            'size' => 10,
            'rgxp' => 'datim',
        ],
        'sql'       => "int(10) unsigned NOT NULL default '0'",
    ],
    'disqus_counter'             => [
        'exclude'   => 0,
        'inputType' => 'text',
        'eval'      => [
            'size' => 4,
            'rgxp' => 'alnum',
        ],
        'sql'       => ['type' => 'integer', 'default' => '0'],
    ],
    'disqus_updated_at'          => [
        'exclude'   => 0,
        'inputType' => 'text',
        'eval'      => [
            'size' => 10,
            'rgxp' => 'datim',
        ],
        'sql'       => "int(10) unsigned NOT NULL default '0'",
    ],
    'google_analytic_counter'    => [
        'exclude'   => 0,
        'inputType' => 'text',
        'eval'      => [
            'size' => 4,
            'rgxp' => 'alnum',
        ],
        'sql'       => ['type' => 'integer', 'default' => '0'],
    ],
    'google_analytic_updated_at' => [
        'exclude'   => 0,
        'inputType' => 'text',
        'eval'      => [
            'size' => 10,
            'rgxp' => 'datim',
        ],
        'sql'       => "int(10) unsigned NOT NULL default '0'",
    ],
    'info_box_selector'          => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['info_box_selector'],
        'inputType' => 'radio',
        'options'   => ['info_box_none', 'info_box_text', 'info_box_link'],
        'default'   => 'info_box_none',
        'exclude'   => true,
        'filter'    => true,
        'sql'       => "varchar(20) NOT NULL default ''",
        'eval'      => ['submitOnChange' => true],
    ],
    'info_box_text_header'       => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['info_box_text_header'],
        'inputType' => 'text',
        'eval'      => ['mandatory' => true],
        'sql'       => "varchar(255) NOT NULL DEFAULT ''",
    ],
    'info_box_text_text'         => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['info_box_text'],
        'inputType' => 'textarea',
        'eval'      => ['rte' => 'tinyMCE', 'tl_class' => 'clr', 'mandatory' => true],
        'sql'       => "text NULL",
    ],
    'info_box_text_link'         => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['info_box_text_link'],
        'inputType' => 'text',
        'sql'       => "varchar(255) NOT NULL DEFAULT ''",
    ],
    'info_box_text_link_text'    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['info_box_text_link_text'],
        'inputType' => 'text',
        'sql'       => "varchar(255) NOT NULL DEFAULT ''",
    ],
    'info_box_link'              => [
        'label'            => &$GLOBALS['TL_LANG']['tl_news']['info_box_link'],
        'inputType'        => 'select',
        'exclude'          => true,
        'options_callback' => ['HeimrichHannot\NewsBundle\Backend\Module', 'getArticleAlias'],
        'eval'             => ['chosen' => true, 'mandatory' => true],
        'sql'              => "int(10) NOT NULL default '0'",
    ],
];

$dc['fields'] = array_merge($dc['fields'], $fields);
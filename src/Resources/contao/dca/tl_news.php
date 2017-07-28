<?php

$dc = &$GLOBALS['TL_DCA']['tl_news'];

/**
 * Selectors
 */
$dc['palettes']['__selector__'][] = 'add_contact_box';
$dc['palettes']['__selector__'][] = 'add_readers_survey';


/**
 * Palettes
 */
$dc['palettes']['default'] = str_replace('{date_legend}','{tags_legend:hide},tags;{contact_box_legend},add_contact_box;{readers_survey_legend:hide},add_readers_survey;{date_legend}',$dc['palettes']['default']);

/**
 * Subpalettes
 */
$dc['subpalettes']['add_contact_box']    = 'contact_box_members,contact_box_header,contact_box_topic,contact_box_title,add_contact_box_link';
$dc['subpalettes']['add_readers_survey'] = 'readers_survey_question, readers_survey_answers';

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
                    'readers_survey_answer' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_news']['readers_survey_answer'],
                        'inputType' => 'text',
                        'eval'      => ['groupStyle' => 'width:800px', 'mandatory' => true],
                    ],
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
];

$dc['fields'] = array_merge($dc['fields'], $fields);
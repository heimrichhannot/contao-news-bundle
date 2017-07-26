<?php

$dc = &$GLOBALS['TL_DCA']['tl_news'];

/**
 * Selectors
 */
$dc['palettes']['__selector__'][] = 'add_contact_box';


/**
 * Palettes
 */
$dc['palettes']['default'] = str_replace('{date_legend}', '{tags_legend:hide},tags;{contact_box_legend},add_contact_box;{date_legend}', $dc['palettes']['default']);

/**
 * Subpalettes
 */
$dc['subpalettes']['add_contact_box'] = 'contact_box_members,contact_box_header,contact_box_topic,contact_box_title,add_contact_box_link';

/**
 * Fields
 */
$fields = [
    'tags'                 => [
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
    'add_contact_box'      => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['add_contact_box'],
        'inputType' => 'checkbox',
        'exclude'   => true,
        'sql'       => "varchar(255) NOT NULL default ''",
        'eval'      => ['submitOnChange' => true],
    ],
    'contact_box_members'  => [
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
    'contact_box_header'   => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contact_box_header'],
        'inputType' => 'text',
        'sql'       => "varchar(255) NOT NULL default ''",
        'eval'      => ['mandatory' => true],
    ],
    'contact_box_topic'    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contact_box_topic'],
        'inputType' => 'text',
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'contact_box_title'    => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news']['contact_box_title'],
        'inputType' => 'text',
        'sql'       => "varchar(255) NOT NULL default ''",
    ],
    'add_contact_box_link' => [
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
];

$dc['fields'] = array_merge($dc['fields'], $fields);
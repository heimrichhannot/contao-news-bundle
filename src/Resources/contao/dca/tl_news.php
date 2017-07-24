<?php

$dc = &$GLOBALS['TL_DCA']['tl_news'];

$dc['palettes']['default'] = str_replace('{date_legend}', '{tags_legend:hide},tags;{date_legend}', $dc['palettes']['default']);

$fields = [
    'tags'        => [
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
];

$dc['fields'] = array_merge($dc['fields'], $fields);
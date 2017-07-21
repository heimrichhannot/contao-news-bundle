<?php

$dc = &$GLOBALS['TL_DCA']['tl_news'];

$dc['palettes']['default'] = str_replace('{date_legend}', '{tags_legend},n_expert;{date_legend}', $dc['palettes']['default']);

$fields = [
    'tags' => [
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
    'n_expert' => [
        'label'            => &$GLOBALS['TL_LANG']['tl_news']['n_expert'],
        'inputType'        => 'tagsinput',
        'sql'              => "varchar(10) NOT NULL default '",
        'options_callback' => ['HeimrichHannot\NewsBundle\Backend\Module', 'getLawyers', 'value'],
        'eval'             => [
            'placeholder' => &$GLOBALS['TL_LANG']['tl_news']['placeholders']['n_expert'],
            'freeInput'   => false,
            'mode'        => \TagsInput::MODE_REMOTE,
            'remote'      => [
                'queryPattern' => '%QUERY%',
                'limit'        => 5,
            ],
        ],
    ],
];

$dc['fields'] = array_merge($dc['fields'], $fields);
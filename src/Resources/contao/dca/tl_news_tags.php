<?php

$GLOBALS['TL_DCA']['tl_news_tags'] = [
    // Config
    'config' => [
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'notCopyable'      => true,
        'sql'              => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    // Fields
    'fields' => [
        'id'         => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'news_id'    => [
            'sql' => ['type' => 'integer', 'unsigned' => true],
        ],
        'cfg_tag_id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true],
        ],
    ],
];

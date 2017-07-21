<?php

$dc = &$GLOBALS['TL_DCA']['tl_news'];

$dc['palettes']['default'] = str_replace('{date_legend}', '{tags_legend},tags;{date_legend}', $dc['palettes']['default']);

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
];

$dc['fields'] = array_merge($dc['fields'], $fields);

//$GLOBALS['TL_DCA']['tl_table_one']['fields']['my_field']['relation'] = array(
//    'type'            => 'haste-ManyToMany',
//    'load'            => 'lazy',
//    'table'           => 'tl_table_two', // the related table
//    'reference'       => 'id', // current table field (optional)
//    'referenceSql'    => "int(10) unsigned NOT NULL default '0'", // current table field sql definition (optional)
//    'referenceColumn' => 'my_reference_field', // a custom column name in relation table (optional)
//    'field'           => 'id', // related table field (optional)
//    'fieldSql'        => "int(10) unsigned NOT NULL default '0'", // related table field sql definition (optional)
//    'fieldColumn'     => 'my_related_field', // a custom column name in relation table (optional)
//    'relationTable'   => '', // custom relation table name (optional)
//    'forceSave'       => true // false by default. If set to true it does not only store the values in the relation tables but also the "my_relation" field
//    'bidirectional' => true // false by default. If set to true relations are handled bidirectional (e.g. project A is related to project B but project B is also related to project A)
//);
<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

$dc = &$GLOBALS['TL_DCA']['tl_news_archive'];

/**
 * Config
 */
$dc['list']['sorting']['mode']        = 2;
$dc['list']['sorting']['fields']      = ['root', 'title'];
$dc['list']['sorting']['panelLayout'] = 'filter;sort,search,limit';
/**
 * Add a global operation to tl_news_archive
 */
array_insert(
    $GLOBALS['TL_DCA']['tl_news_archive']['list']['global_operations'],
    1,
    [
        'lists' => [
            'label'      => &$GLOBALS['TL_LANG']['tl_news_archive']['lists'],
            'href'       => 'table=tl_news_list_archive',
            'icon'       => 'folderC.svg',
            'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="c"',
        ],
    ]
);

/**
 * Palettes
 */
$dc['palettes']['__selector__'][] = 'limitInputCharacterLength';
$dc['palettes']['default']        = str_replace('allowComments', 'allowComments;{palettes_legend},addCustomNewsPalettes;', $dc['palettes']['default']);
$dc['palettes']['default']        = str_replace('jumpTo;', 'jumpTo;{root_legend},root;', $dc['palettes']['default']);
$dc['palettes']['default']        = str_replace('jumpTo;', 'jumpTo;{input_legend},limitInputCharacterLength;', $dc['palettes']['default']);

/**
 * Selectors
 */
$dc['palettes']['__selector__'][] = 'addCustomNewsPalettes';

/**
 * Subpalettes
 */
$dc['subpalettes']['addCustomNewsPalettes']     = 'customNewsPalettes';
$dc['subpalettes']['limitInputCharacterLength'] = 'inputCharacterLengths';

/**
 * Fields
 */
$fields = [
    'customNewsPalettes'        => [
        'label'            => &$GLOBALS['TL_LANG']['tl_news_archive']['customNewsPalettes'],
        'inputType'        => 'select',
        'options_callback' => ['huh.news.listener.callback.news', 'getNewsPalettes'],
        'eval'             => ['tl_class' => 'w50 clr', 'includeBlankOption' => true],
        'exclude'          => true,
        'sql'              => "varchar(50) NOT NULL default ''",
    ],
    'addCustomNewsPalettes'     => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['addCustomNewsPalettes'],
        'inputType' => 'checkbox',
        'exclude'   => true,
        'sql'       => "char(1) NOT NULL default ''",
        'eval'      => ['submitOnChange' => true],
    ],
    'root'                      => [
        'label'            => &$GLOBALS['TL_LANG']['tl_news_archive']['root'],
        'inputType'        => 'select',
        'sorting'          => true,
        'filter'           => true,
        'flag'             => 11,
        'exclude'          => true,
        'options_callback' => ['huh.news.backend.news_archive', 'getRootPages'],
        'eval'             => ['includeBlankOption' => true, 'tl_class' => 'w50'],
        'sql'              => "int(10) unsigned NOT NULL default '0'",
    ],
    'addDummyImage'             => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['addDummyImage'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'dummyImageSingleSRC'       => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['dummyImageSingleSRC'],
        'exclude'   => true,
        'inputType' => 'fileTree',
        'eval'      => ['filesOnly' => true, 'fieldType' => 'radio', 'mandatory' => true, 'tl_class' => 'clr'],
        'sql'       => "binary(16) NULL",
    ],
    'limitInputCharacterLength' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['limitInputCharacterLength'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''",
    ],
    'inputCharacterLengths'     => [
        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['inputCharacterLengths'],
        'exclude'   => true,
        'inputType' => 'multiColumnEditor',
        'eval'      => [
            'tl_class'          => 'clr',
            'multiColumnEditor' => [
                'minRowCount' => 0,
                'fields'      => [
                    'field'  => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['inputCharacterLengths']['field'],
                        'options_callback'   => function(\Contao\DataContainer $dc){
                            \Contao\Controller::loadDataContainer('tl_news');
                            \Contao\System::loadLanguageFile('tl_news');

                            $fields = [];

                            foreach ($GLOBALS['TL_DCA']['tl_news']['fields'] as $name => $data)
                            {
                                if(!in_array($data['inputType'], ['text', 'textarea']) || isset($data['eval']['rgxp']))
                                {
                                    continue;
                                }

                                $fields[$name] = isset($data['label'][0]) ? sprintf('%s [%s]', $data['label'][0], $name) : $name;
                            }

                            return $fields;
                        },
                        'reference' => &$GLOBALS['TL_LANG']['tl_news'],
                        'inputType' => 'select',
                        'eval'      => ['chosen' => true, 'style' => 'width: 250px'],
                    ],
                    'length' => [
                        'label'     => &$GLOBALS['TL_LANG']['tl_news_archive']['inputCharacterLengths']['length'],
                        'inputType' => 'text',
                        'eval'      => ['style' => 'width: 250px', 'rgxp' => 'digit'],
                    ],
                ],
            ],
        ],
        'sql'       => "blob NULL",
    ],

];

$dc['fields'] = array_merge($dc['fields'], $fields);

$dc['fields']['title']['sorting'] = true;

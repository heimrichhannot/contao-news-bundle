<?php

$GLOBALS['TL_DCA']['tl_news_list_archive'] = [
    'config' => [
        'dataContainer'     => 'Table',
        'ctable'            => ['tl_news_list'],
        'switchToEdit'                => true,
        'enableVersioning'  => true,
        'onload_callback' => [
            ['tl_news_list_archive', 'checkPermission'],
        ],
        'onsubmit_callback' => [
            ['HeimrichHannot\Haste\Dca\General', 'setDateAdded'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ],
    'list' => [
        'label' => [
            'fields' => ['title'],
            'format' => '%s'
        ],
        'sorting'           => [
            'mode'                  => 0,
            'headerFields'          => ['title'],
            'panelLayout'           => 'filter;search,limit'
        ],
        'global_operations' => [
            'all'    => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ],
        ],
        'operations' => [
            'edit' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_news_list_archive']['edit'],
                'href'                => 'table=tl_news_list',
                'icon'                => 'edit.gif'
            ],
            'editheader' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_news_list_archive']['editheader'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif',
                'button_callback'     => ['tl_news_list_archive', 'editHeader']
            ],
            'copy' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_news_list_archive']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif',
                'button_callback'     => ['tl_news_list_archive', 'copyArchive']
            ],
            'delete' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_news_list_archive']['copy'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
                'button_callback'     => ['tl_news_list_archive', 'deleteArchive']
            ],
            'toggle' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_news_list_archive']['toggle'],
                'href'                => 'act=toggle',
                'icon'                => 'toggle.gif'
            ],
            'show' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_news_list_archive']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            ],
        ]
    ],
    'palettes' => [
        '__selector__' => [],
        'default' => '{general_legend},title;'
    ],
    'fields'   => [
        'id' => [
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_news_list_archive']['tstamp'],
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ],
        'dateAdded' => [
            'label'                   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting'                 => true,
            'flag'                    => 6,
            'eval'                    => ['rgxp'=>'datim', 'doNotCopy' => true],
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ],
        'title' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_news_list_archive']['title'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'inputType'               => 'text',
            'eval'                    => ['mandatory' => true, 'tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
    ]
];

class tl_news_list_archive extends \Backend
{
    public function checkPermission()
    {
        $user     = \BackendUser::getInstance();
        $database = \Database::getInstance();
        $bundles  = \System::getContainer()->getParameter('kernel.bundles');
        
        // HOOK: comments extension required
        if (!isset($bundles['ContaoCommentsBundle']))
        {
            unset($GLOBALS['TL_DCA']['tl_news_list_archive']['fields']['allowComments']);
        }
        
        if ($user->isAdmin)
        {
            return;
        }
        
        // Set root IDs
        if (!is_array($user->newslists) || empty($user->newslists))
        {
            $root = [0];
        }
        else
        {
            $root = $user->newslists;
        }
        
        $GLOBALS['TL_DCA']['tl_news_list_archive']['list']['sorting']['root'] = $root;
        
        // Check permissions to add archives
        if (!$user->hasAccess('create', 'newslistp'))
        {
            $GLOBALS['TL_DCA']['tl_news_list_archive']['config']['closed'] = true;
        }
        
        /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $objSession */
        $objSession = \System::getContainer()->get('session');
        
        // Check current action
        switch (\Input::get('act'))
        {
            case 'create':
            case 'select':
                // Allow
                break;
        
            case 'edit':
                // Dynamically add the record to the user profile
                if (!in_array(\Input::get('id'), $root))
                {
                    /** @var \Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface $sessionBag */
                    $sessionBag = $objSession->getBag('contao_backend');
            
                    $arrNew = $sessionBag->get('new_records');
            
                    if (is_array($arrNew['tl_news_list_archive']) && in_array(\Input::get('id'), $arrNew['tl_news_list_archive']))
                    {
                        // Add the permissions on group level
                        if ($user->inherit != 'custom')
                        {
                            $objGroup = $database->execute("SELECT id, newslists, newslistp FROM tl_user_group WHERE id IN(" . implode(',', array_map('intval', $user->groups)) . ")");
                
                            while ($objGroup->next())
                            {
                                $arrModulep = StringUtil::deserialize($objGroup->newslistp);
                
                                if (is_array($arrModulep) && in_array('create', $arrModulep))
                                {
                                    $arrModules = StringUtil::deserialize($objGroup->newslists, true);
                                    $arrModules[] = \Input::get('id');
                
                                    $database->prepare("UPDATE tl_user_group SET newslists=? WHERE id=?")->execute(serialize($arrModules), $objGroup->id);
                                }
                            }
                        }
                
                        // Add the permissions on user level
                        if ($user->inherit != 'group')
                        {
                            $user = $database->prepare("SELECT newslists, newslistp FROM tl_user WHERE id=?")
                            ->limit(1)
                            ->execute($user->id);
                
                            $arrModulep = StringUtil::deserialize($user->newslistp);
                
                            if (is_array($arrModulep) && in_array('create', $arrModulep))
                            {
                                $arrModules = StringUtil::deserialize($user->newslists, true);
                                $arrModules[] = \Input::get('id');
                
                                $database->prepare("UPDATE tl_user SET newslists=? WHERE id=?")
                                ->execute(serialize($arrModules), $user->id);
                            }
                        }
                
                        // Add the new element to the user object
                        $root[] = \Input::get('id');
                        $user->newslists = $root;
                    }
                }
                // No break;
        
            case 'copy':
            case 'delete':
            case 'show':
                if (!in_array(\Input::get('id'), $root) || (\Input::get('act') == 'delete' && !$user->hasAccess('delete', 'newslistp')))
                {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to ' . \Input::get('act') . ' news_list_archive ID ' . \Input::get('id') . '.');
                }
                break;
    
            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
                $session = $objSession->all();
                if (\Input::get('act') == 'deleteAll' && !$user->hasAccess('delete', 'newslistp'))
                {
                    $session['CURRENT']['IDS'] = [];
                }
                else
                {
                    $session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $root);
                }
                $objSession->replace($session);
                break;
            
            default:
                if (strlen(\Input::get('act')))
                {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to ' . \Input::get('act') . ' news_list_archives.');
                }
                break;
        }
    }

    public function editHeader($row, $href, $label, $title, $icon, $attributes)
    {
        return \BackendUser::getInstance()->canEditFieldsOf('tl_news_list_archive') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }

    public function copyArchive($row, $href, $label, $title, $icon, $attributes)
    {
        return \BackendUser::getInstance()->hasAccess('create', 'newslistp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }

    public function deleteArchive($row, $href, $label, $title, $icon, $attributes)
    {
        return \BackendUser::getInstance()->hasAccess('delete', 'newslistp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : Image::getHtml(preg_replace('/\.svg$/i', '_.svg', $icon)).' ';
    }
}

<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Backend;

use Contao\System;
use HeimrichHannot\NewsBundle\Model\NewsModel;

class NewsList extends \Contao\Backend
{
    const MODE_MANUAL = 'manual';
    const MODE_AUTO_ITEM = 'auto_item';

    const MODES = [
        self::MODE_MANUAL,
        self::MODE_AUTO_ITEM,
    ];

    public static function generateAlias($varValue, \DataContainer $objDc)
    {
        if (null === ($objNewsList = \HeimrichHannot\NewsBundle\Model\NewsListModel::findByPk($objDc->id))) {
            return $varValue;
        }

        return System::getContainer()->get('huh.utils.dca')->generateAlias($varValue, $objDc->id, 'tl_news_list', $objNewsList->title, false);
    }

    public function checkPermission()
    {
        $user = \BackendUser::getInstance();
        $database = \Database::getInstance();

        if ($user->isAdmin) {
            return;
        }

        // Set the root IDs
        if (!\is_array($user->newslists) || empty($user->newslists)) {
            $root = [0];
        } else {
            $root = $user->newslists;
        }

        $id = \strlen(\Input::get('id')) ? \Input::get('id') : CURRENT_ID;

        // Check current action
        switch (\Input::get('act')) {
            case 'paste':
                // Allow
                break;

            case 'create':
                if (!\strlen(\Input::get('pid')) || !\in_array(\Input::get('pid'), $root, true)) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to create news_list items in news_list archive ID '.\Input::get('pid').'.');
                }

                break;

            case 'cut':
            case 'copy':
                if (!\in_array(\Input::get('pid'), $root, true)) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to '.\Input::get('act').' news_list item ID '.$id.' to news_list archive ID '.\Input::get('pid').'.');
                }
            // no break STATEMENT HERE

            case 'edit':
            case 'show':
            case 'delete':
            case 'toggle':
            case 'feature':
                $objArchive = $database->prepare('SELECT pid FROM tl_news_list WHERE id=?')->limit(1)->execute($id);

                if ($objArchive->numRows < 1) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Invalid news_list item ID '.$id.'.');
                }

                if (!\in_array($objArchive->pid, $root, true)) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to '.\Input::get('act').' news_list item ID '.$id.' of news_list archive ID '.$objArchive->pid.'.');
                }

                break;

            case 'select':
            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
            case 'cutAll':
            case 'copyAll':
                if (!\in_array($id, $root, true)) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to access news_list archive ID '.$id.'.');
                }

                $objArchive = $database->prepare('SELECT id FROM tl_news_list WHERE pid=?')->execute($id);

                if ($objArchive->numRows < 1) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Invalid news_list archive ID '.$id.'.');
                }

                /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
                $session = \System::getContainer()->get('session');

                $session = $session->all();
                $session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $objArchive->fetchEach('id'));
                $session->replace($session);

                break;

            default:
                if (\strlen(\Input::get('act'))) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Invalid command "'.\Input::get('act').'".');
                } elseif (!\in_array($id, $root, true)) {
                    throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to access news_list archive ID '.$id.'.');
                }

                break;
        }
    }

    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        $user = \BackendUser::getInstance();

        if (\strlen(\Input::get('tid'))) {
            $this->toggleVisibility(\Input::get('tid'), (1 == \Input::get('state')), (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$user->hasAccess('tl_news_list::published', 'alexf')) {
            return '';
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

        if (!$row['published']) {
            $icon = 'invisible.svg';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.\StringUtil::specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label, 'data-state="'.($row['published'] ? 1 : 0).'"').'</a> ';
    }

    public function toggleVisibility($intId, $blnVisible, \DataContainer $dc = null)
    {
        $user = \BackendUser::getInstance();
        $database = \Database::getInstance();

        // Set the ID and action
        \Input::setGet('id', $intId);
        \Input::setGet('act', 'toggle');

        if ($dc) {
            $dc->id = $intId; // see #8043
        }

        // Trigger the onload_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_news_list']['config']['onload_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_news_list']['config']['onload_callback'] as $callback) {
                if (\is_array($callback)) {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                } elseif (\is_callable($callback)) {
                    $callback($dc);
                }
            }
        }

        // Check the field access
        if (!$user->hasAccess('tl_news_list::published', 'alexf')) {
            throw new \Contao\CoreBundle\Exception\AccessDeniedException('Not enough permissions to publish/unpublish news_list item ID '.$intId.'.');
        }

        // Set the current record
        if ($dc) {
            $objRow = $database->prepare('SELECT * FROM tl_news_list WHERE id=?')->limit(1)->execute($intId);

            if ($objRow->numRows) {
                $dc->activeRecord = $objRow;
            }
        }

        $objVersions = new \Versions('tl_news_list', $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_news_list']['fields']['published']['save_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_news_list']['fields']['published']['save_callback'] as $callback) {
                if (\is_array($callback)) {
                    $this->import($callback[0]);
                    $blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, $dc);
                } elseif (\is_callable($callback)) {
                    $blnVisible = $callback($blnVisible, $dc);
                }
            }
        }

        $time = time();

        // Update the database
        $database->prepare("UPDATE tl_news_list SET tstamp=$time, published='".($blnVisible ? '1' : '')."' WHERE id=?")->execute($intId);

        if ($dc) {
            $dc->activeRecord->tstamp = $time;
            $dc->activeRecord->published = ($blnVisible ? '1' : '');
        }

        // Trigger the onsubmit_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_news_list']['config']['onsubmit_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_news_list']['config']['onsubmit_callback'] as $callback) {
                if (\is_array($callback)) {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                } elseif (\is_callable($callback)) {
                    $callback($dc);
                }
            }
        }

        $objVersions->create();
    }

    public function copyList($row, $href, $label, $title, $icon, $attributes)
    {
        return \BackendUser::getInstance()->hasAccess('create', 'newslistp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.svg/i', '_.svg', $icon)).' ';
    }

    public function deleteList($row, $href, $label, $title, $icon, $attributes)
    {
        return \BackendUser::getInstance()->hasAccess('delete', 'newslistp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.\Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.svg/i', '_.svg', $icon)).' ';
    }

    public function getNewsOptions(\DataContainer $dc)
    {
        $options = [];

        $objNews = NewsModel::findAll(['order' => 'time DESC']);

        if (null === $objNews) {
            return $options;
        }

        while ($objNews->next()) {
            if (null === ($objArchive = $objNews->getRelated('pid'))) {
                continue;
            }

            $options[$objArchive->title][$objNews->id] = $objNews->headline;
        }

        return $options;
    }

    /**
     * Generate the label for one news item.
     *
     * @param array
     * @param string
     * @param object
     * @param string
     *
     * @return string
     */
    public function generateNewsItemLabel($arrRow, $strLabel, $objDca, $strAttributes)
    {
        $objNews = NewsModel::findByPk($arrRow['news_list_news']);

        if (null === $objNews) {
            return $strLabel;
        }

        $strLabel = $objNews->headline.' [ID: '.$objNews->id.']';

        if (null !== ($objArchive = $objNews->getRelated('pid'))) {
            $strLabel .= ' - '.$objArchive->title;
        }

        return $strLabel;
    }
}

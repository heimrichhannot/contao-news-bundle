<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\Backend;

use Contao\CoreBundle\Monolog\ContaoContext;
use HeimrichHannot\FieldPalette\FieldPaletteModel;
use Psr\Log\LogLevel;

class NewsList extends \Contao\Backend
{
    public function checkPermission()
    {
        $objUser     = \BackendUser::getInstance();
        $objSession  = \Session::getInstance();
        $objDatabase = \Database::getInstance();

        if ($objUser->isAdmin)
        {
            return;
        }
        // Set root IDs
        if (!is_array($objUser->newslists) || empty($objUser->newslists))
        {
            $root = [0];
        }
        else
        {
            $root = $objUser->newslists;
        }
        $GLOBALS['TL_DCA']['tl_news_list']['list']['sorting']['root'] = $root;
        // Check permissions to add archives
        if (!$objUser->hasAccess('create', 'newslistp'))
        {
            $GLOBALS['TL_DCA']['tl_news_list']['config']['closed'] = true;
        }
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
                    $arrNew = $objSession->get('new_records');
                    if (is_array($arrNew['tl_news_list']) && in_array(\Input::get('id'), $arrNew['tl_news_list']))
                    {
                        // Add permissions on user level
                        if ($objUser->inherit == 'custom' || !$objUser->groups[0])
                        {
                            $objUser    = $objDatabase->prepare("SELECT newslists, newslistp FROM tl_user WHERE id=?")->limit(1)->execute($objUser->id);
                            $arrModulep = deserialize($objUser->newslistp);
                            if (is_array($arrModulep) && in_array('create', $arrModulep))
                            {
                                $arrModules   = deserialize($objUser->newslists);
                                $arrModules[] = \Input::get('id');
                                $objDatabase->prepare("UPDATE tl_user SET newslists=? WHERE id=?")->execute(serialize($arrModules), $objUser->id);
                            }
                        } // Add permissions on group level
                        elseif ($objUser->groups[0] > 0)
                        {
                            $objGroup   = $objDatabase->prepare("SELECT newslists, newslistp FROM tl_user_group WHERE id=?")->limit(1)->execute($objUser->groups[0]);
                            $arrModulep = deserialize($objGroup->newslistp);
                            if (is_array($arrModulep) && in_array('create', $arrModulep))
                            {
                                $arrModules   = deserialize($objGroup->newslists);
                                $arrModules[] = \Input::get('id');
                                $objDatabase->prepare("UPDATE tl_user_group SET newslists=? WHERE id=?")->execute(serialize($arrModules), $objUser->groups[0]);
                            }
                        }
                        // Add new element to the user object
                        $root[]          = \Input::get('id');
                        $objUser->modals = $root;
                    }
                }
            // No break;
            case 'copy':
            case 'delete':
            case 'show':
                if (!in_array(\Input::get('id'), $root) || (\Input::get('act') == 'delete' && !$objUser->hasAccess('delete', 'newslistp')))
                {
                    \System::getContainer()->get('monolog.logger.contao')->log(
                        LogLevel::ERROR,
                        'Not enough permissions to ' . \Input::get('act') . ' news list ID "' . \Input::get('id') . '"',
                        ['contao' => new ContaoContext(__METHOD__, TL_ERROR)]
                    );

                    \Controller::redirect('contao/main.php?act=error');
                }
                break;
            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
                $session = $objSession->getData();
                if (\Input::get('act') == 'deleteAll' && !$objUser->hasAccess('delete', 'newslistp'))
                {
                    $session['CURRENT']['IDS'] = [];
                }
                else
                {
                    $session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $root);
                }
                $objSession->setData($session);
                break;
            default:
                if (strlen(\Input::get('act')))
                {
                    \System::getContainer()->get('monolog.logger.contao')->log(
                        LogLevel::ERROR,
                        'Not enough permissions to ' . \Input::get('act') . ' news list',
                        ['contao' => new ContaoContext(__METHOD__, TL_ERROR)]
                    );
                    \Controller::redirect('contao/main.php?act=error');
                }
                break;
        }
    }

    public function toggleList($row, $href, $label, $title, $icon, $attributes)
    {
        $objUser = \BackendUser::getInstance();
        if (strlen(\Input::get('tid')))
        {
            $this->toggleVisibility(\Input::get('tid'), (\Input::get('state') == 1));
            \Controller::redirect($this->getReferer());
        }
        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$objUser->isAdmin && !$objUser->hasAccess('tl_news_list::published', 'alexf'))
        {
            return '';
        }
        $href .= '&amp;tid=' . $row['id'] . '&amp;state=' . ($row['published'] ? '' : 1);
        if (!$row['published'])
        {
            $icon = 'invisible.gif';
        }

        return '<a href="' . $this->addToUrl($href) . '" title="' . specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> ';
    }

    public function toggleVisibility($intId, $blnVisible)
    {
        $objUser     = \BackendUser::getInstance();
        $objDatabase = \Database::getInstance();
        // Check permissions to publish
        if (!$objUser->isAdmin && !$objUser->hasAccess('tl_modal::published', 'alexf'))
        {
            \System::getContainer()->get('monolog.logger.contao')->log(
                LogLevel::ERROR,
                'Not enough permissions to publish/unpublish item ID "' . $intId . '"',
                ['contao' => new ContaoContext(__METHOD__, TL_ERROR)]
            );

            \Controller::redirect('contao/main.php?act=error');
        }
        $objVersions = new \Versions('tl_modal', $intId);
        $objVersions->initialize();
        // Trigger the save_callback
        if (is_array($GLOBALS['TL_DCA']['tl_modal']['fields']['published']['save_callback']))
        {
            foreach ($GLOBALS['TL_DCA']['tl_modal']['fields']['published']['save_callback'] as $callback)
            {
                $this->import($callback[0]);
                $blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, $this);
            }
        }
        // Update the database
        $objDatabase->prepare("UPDATE tl_news_list SET tstamp=" . time() . ", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")->execute($intId);
        $objVersions->create();

        \System::getContainer()->get('monolog.logger.contao')->log(
            LogLevel::INFO,
            'A new version of record "tl_news_list.id=' . $intId . '" has been created' . $this->getParentEntries(
                'tl_modal',
                $intId
            ),
            ['contao' => new ContaoContext(__METHOD__, TL_GENERAL)]
        );
    }

    public function copyList($row, $href, $label, $title, $icon, $attributes)
    {
        return \BackendUser::getInstance()->hasAccess('create', 'newslistp')
            ? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label)
              . '</a> '
            : \Image::getHtml(
                preg_replace('/\.svg/i', '_.svg', $icon)
            ) . ' ';
    }

    public function deleteList($row, $href, $label, $title, $icon, $attributes)
    {
        return \BackendUser::getInstance()->hasAccess('delete', 'newslistp')
            ? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label)
              . '</a> '
            : \Image::getHtml(
                preg_replace('/\.svg/i', '_.svg', $icon)
            ) . ' ';
    }

    public function getNewsOptions(\DataContainer $dc)
    {
        $options = [];

        $objNews = \NewsModel::findAll(['order' => 'time DESC']);

        if ($objNews === null)
        {
            return $options;
        }

        while ($objNews->next())
        {
            if (($objArchive = $objNews->getRelated('pid')) === null)
            {
                continue;
            }

            $options[$objArchive->title][$objNews->id] = $objNews->headline;
        }

        return $options;
    }

    /**
     * Generate the label for one news item
     * @param array
     * @param string
     * @param object
     * @param string
     * @return string
     */
    public function generateNewsItemLabel($arrRow, $strLabel, $objDca, $strAttributes)
    {
        $objNews = \NewsModel::findByPk($arrRow['news_list_news']);

        if($objNews === null)
        {
            return $strLabel;
        }

        $strLabel = $objNews->headline . ' [ID: ' .  $objNews->id . ']';

        if(($objArchive = $objNews->getRelated('pid')) !== null)
        {
            $strLabel .= ' - ' . $objArchive->title;
        }

        return $strLabel;
    }
}
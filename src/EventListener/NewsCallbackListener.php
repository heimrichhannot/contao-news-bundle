<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\NewsBundle\EventListener;


use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NewsCallbackListener
{
    /**
     * @var ContaoFramework
     */
    private $framework;

    public function __construct(ContaoFramework $framework)
    {

        $this->framework = $framework;
    }


    public function getNewsPalettes(DataContainer $dc)
    {
        $arrOptions = [];
        $this->framework->getAdapter(Controller::class)->loadDataContainer('tl_news');
        $arrPalettes = $GLOBALS['TL_DCA']['tl_news']['palettes'];
        if (!is_array($arrPalettes)) {
            return $arrOptions;
        }
        foreach ($arrPalettes as $strName => $strPalette) {
            if (in_array($strName, ['__selector__', 'internal', 'external', 'default'])) {
                continue;
            }
            $arrOptions[$strName] = $strName;
        }

        return $arrOptions;
    }

}
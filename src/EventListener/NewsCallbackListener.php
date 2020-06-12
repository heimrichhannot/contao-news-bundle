<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\EventListener;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;

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

        if (!\is_array($arrPalettes)) {
            return $arrOptions;
        }

        foreach ($arrPalettes as $strName => $strPalette) {
            if (\in_array($strName, ['__selector__', 'internal', 'external', 'default'], true)) {
                continue;
            }
            $arrOptions[$strName] = $strName;
        }

        return $arrOptions;
    }
}

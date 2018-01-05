<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 08.06.17
 * Time: 13:42
 */

namespace HeimrichHannot\NewsBundle;


class Module extends \Backend
{
    public function getNewsPalettes(\Contao\DataContainer $dc)
    {
        $arrOptions = [];
        \Controller::loadDataContainer('tl_news');
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
<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 08.06.17
 * Time: 13:42
 */

namespace Dav\LawyerSearchBundle\Backend;


use Dav\LawyerSearchBundle\Choices\DistanceChoice;
use Dav\LawyerSearchBundle\Component\RestClient;

class Module extends \Backend
{
    public function getListExtendedModules(\DataContainer $dc)
    {
        return static::getModuleOptions('ls_list_extended');
    }

    public function getDistanceChoices(\DataContainer $dc)
    {
        return array_flip(DistanceChoice::create()->getChoices());
    }

    public function getSearchModules(\DataContainer $dc)
    {
        return static::getModuleOptions('ls_search');
    }

    public function getWatchlistModules(\DataContainer $dc)
    {
        return static::getModuleOptions('ls_watchlist');
    }

    public function getReaderModules(\DataContainer $dc)
    {
        return static::getModuleOptions('ls_reader');
    }

    public function getListModules(\DataContainer $dc)
    {
        return static::getModuleOptions('ls_list');
    }

    protected static function getModuleOptions($strType)
    {
        $arrOptions = [];

        $objModules = \ModuleModel::findByType($strType);

        if ($objModules === null)
        {
            return $arrOptions;
        }

        return $objModules->fetchEach('name');
    }
}
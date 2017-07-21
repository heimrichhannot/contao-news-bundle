<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 08.06.17
 * Time: 13:42
 */

namespace HeimrichHannot\NewsBundle\Backend;


use Dav\LawyerSearchBundle\Component\RestClient;

class Module extends \Backend
{
    public function getLawyers()
    {
        $searchString = $_POST['query'];
        if (empty($searchString) || $searchString == '')
        {
            return [];
        }
        if (count(explode(' ', $searchString)) > 1 && explode(' ', $searchString)[1] !== '')
        {
            $string       = explode(' ', $searchString);
            $searchString = '"first_name":{"l":"' . $string[0] . '""last_name":{"l":"' . $string[1] . '"}';
        }
        else
        {
            $searchString = '"first_name":{"l":"' . $searchString . '"}';

        }
        $restClient = new RestClient();
        $result     = $restClient->query(
            'https://rest.anwaltauskunft.dav.hhdev/lawyers',
            '?where={"profile_disabled":{"e":0},' . $searchString . '}&fields=first_name,last_name,uuid&limit=5'
        );
        $lawyers    = [];
        foreach ($result->lawyers as $lawyer)
        {
            $lawyers[$lawyer->uuid] = $lawyer->first_name . ' ' . $lawyer->last_name;
        }

        return $lawyers;
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
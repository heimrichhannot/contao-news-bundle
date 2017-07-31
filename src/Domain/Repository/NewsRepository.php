<?php

namespace HeimrichHannot\NewsBundle\Domain\Repository;

use HeimrichHannot\NewsBundle\NewsModel;

/**
 * NewsRepository
 *
 */
class NewsRepository extends NewsModel
{
    public function getAllForSocialstatsUpdate($countOnly = false, $offset = false, $limit = false)
    {
        $arrOptions['order'] = 'date ASC';
        $tsPeriod            = time() - (60 * 60 * 24 * 180); // 180 days

        $column = ['date>?', 'hidden=?'];
        $value  = [$tsPeriod, 0];

        if (false !== $offset)
        {
            $arrOptions['offset'] = $offset;
        }
        if (false !== $limit)
        {
            $arrOptions['limit'] = $limit;
        }

        if ($countOnly)
        {
            $result = NewsModel::countBy($column, $value, $arrOptions);
        }
        else
        {
            $result = NewsModel::findBy($column, $value, $arrOptions);
        }

        return $result;
    }
}

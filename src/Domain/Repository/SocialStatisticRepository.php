<?php

namespace HeimrichHannot\NewsBundle\Domain\Repository;

use HeimrichHannot\NewsBundle\NewsModel;

/**
 *
 *
 * @package dav_news
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class SocialStatisticRepository extends NewsModel
{

    static $TYPE_NEWS = 1;

    /**
     * @param $provider
     * @param $type
     * @param $ids
     *
     * @return \Contao\Model\Collection|\Contao\NewsModel|\Contao\NewsModel[]|null
     */
    public function getAllItemsforUpdate($provider, $type, $ids)
    {
        $arrOptions['order'] = $provider . 'UpdatedAt ASC';

        $result = NewsModel::findByAndInIds('type', $type, $ids, $arrOptions);

        return $result;
    }

    public function getEntryByNewsId($newsId)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching($query->logicalAnd([$query->equals('type', self::$TYPE_NEWS), $query->equals('id', (integer) $newsId)]));
        $query->setLimit(1);

        return $query->execute()->getFirst();
    }

    public function getEntryByNewsIds($newsIds = [])
    {
        if (count($newsIds) > 0)
        {
            $query = $this->createQuery();
            $query->getQuerySettings()->setRespectStoragePage(false);
            $query->matching($query->logicalAnd([$query->equals('type', self::$TYPE_NEWS), $query->in('id', $newsIds)]));

            return $query->execute();
        }
        else
        {
            return false;
        }

    }
}

?>
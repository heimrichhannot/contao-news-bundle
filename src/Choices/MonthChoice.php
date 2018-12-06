<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\Choices;

use HeimrichHannot\NewsBundle\Model\NewsModel;

class MonthChoice extends AbstractChoice
{
    /**
     * Current year
     * @var int
     */
    protected $year;

    /**
     * Current year
     * @param $year
     */
    public function setYear($year)
    {
        $this->year     = $year;
        $this->cacheKey .= $year;

        return $this;
    }

    /**
     * @return array
     */
    protected function collectChoices()
    {
        $choices      = [];
        $newsArchives = deserialize($this->filter->getModule()->news_archives, true);

        if (!empty($newsArchives)) {
            $months = NewsModel::getPublishedMonthsByYearAndPids($newsArchives, $this->year);

            foreach ($months as $month) {
                $choices[$month] = 'news.form.filter.choice.month.' . $month;
            }

            $choices = array_flip($choices);
        }

        return $choices;
    }
}
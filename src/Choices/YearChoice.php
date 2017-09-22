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

class YearChoice extends AbstractChoice
{
    /**
     * @return array
     */
    protected function collectChoices()
    {
        $choices      = [];
        $newsArchives = deserialize($this->filter->getModule()->news_archives, true);

        if (empty($newsArchives)) {
            return $choices;
        }

        $choices = NewsModel::getPublishedYearsByPids($newsArchives);
        $choices = array_combine($choices, $choices);

        return $choices;
    }
}
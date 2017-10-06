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

use Contao\NewsArchiveModel;

class ArchivesChoice extends AbstractChoice
{
    /**
     * @return array
     */
    protected function collectChoices()
    {
        $choices      = [];
        $newsArchives = deserialize($this->filter->getModule()->news_archives, true);

        if (empty($newsArchives))
        {
            return $choices;
        }

        foreach ($newsArchives as $newsArchiveId)
        {
            $choices[NewsArchiveModel::findById($newsArchiveId)->title] = NewsArchiveModel::findById($newsArchiveId)->id;
        }

        return $choices;
    }
}
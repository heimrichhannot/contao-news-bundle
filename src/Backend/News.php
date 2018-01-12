<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\Backend;


use HeimrichHannot\Haste\Model\MemberModel;

class News extends \Backend
{
    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    /**
     * Manipulate related news from `tl_news.related_news` remote tagsinput call
     *
     * @param                $arrOption
     * @param \DataContainer $dc
     *
     * @return null
     */
    public function getRelatedNews($arrOption, \DataContainer $dc)
    {
        if ($arrOption['value'] == $dc->id) {
            return null;
        }

        return $arrOption;
    }


    /**
     * get member by last name from input
     *
     * @param \DataContainer $dc
     *
     * @return array
     */
    public function getMembers($arrOption, \DataContainer $dc)
    {
        if ($arrOption['value'] == $dc->id) {
            return null;
        }

        return $arrOption;
    }
}
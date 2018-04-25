<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Backend;

class News extends \Backend
{
    /**
     * Import the back end user object.
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    /**
     * Manipulate related news from `tl_news.related_news` remote tagsinput call.
     *
     * @param                $arrOption
     * @param \DataContainer $dc
     */
    public function getRelatedNews($arrOption, \DataContainer $dc)
    {
        if ($arrOption['value'] == $dc->id) {
            return null;
        }

        return $arrOption;
    }

    /**
     * get member by last name from input.
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

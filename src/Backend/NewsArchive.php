<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Backend;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\DataContainer;
use Contao\Model;
use Contao\PageModel;

class NewsArchive
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Get all available root pages.
     *
     * @return array
     */
    public function getRootPages(DataContainer $dc)
    {
        /** @var PageModel $pages */
        if (null === ($pages = $this->framework->getAdapter(Model::getClassFromTable('tl_page'))->findBy('type', 'root'))) {
            return [];
        }

        return $pages->fetchEach('title');
    }
}

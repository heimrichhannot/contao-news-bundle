<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\NewsFilter;

class NewsFilterModuleRegistry
{
    /**
     * The modules
     * @var array
     */
    private $modules = [];

    /**
     * Add a news filter module
     * @param NewsFilterModule $module
     */
    public function add(NewsFilterModule $module)
    {
        if (isset($this->modules[$module->getAlias()])) {
            return;
        }

        $this->modules[$module->getAlias()] = $module;
    }

    /**
     * Return filter by a given alias
     * @param int $alias
     *
     * @return NewsFilterModule|null The News filter module, or null if it does not exist
     */
    public function get($alias)
    {
        return isset($this->modules[$alias]) ? $this->modules[$alias] : null;
    }
}
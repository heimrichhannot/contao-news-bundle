<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\NewsFilter;


class NewsFilterRegistry
{
    /**
     * Filters.
     *
     * @var array
     */
    private $filters = [];

    /**
     * Add the filter
     *
     * @param NewsFilterInterface $filter
     * @param string $alias
     */
    public function add(NewsFilterInterface $filter, string $alias): void
    {
        $this->filters[$alias] = $filter;
    }

    /**
     * Get the filter.
     *
     * @param string $alias
     *
     * @throws \InvalidArgumentException
     *
     * @return NewsFilterInterface
     */
    public function get(string $alias): NewsFilterInterface
    {
        if (!array_key_exists($alias, $this->filters)) {
            throw new \InvalidArgumentException(sprintf('The filter "%s" does not exist', $alias));
        }

        return $this->filters[$alias];
    }

    /**
     * Get the filters.
     *
     * @return array
     */
    public function getAliases(): array
    {
        return array_keys($this->filters);
    }
}
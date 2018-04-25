<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Event;

use Contao\FrontendTemplate;
use Contao\ModuleNewsList;
use Symfony\Component\EventDispatcher\Event;

class NewsListBeforeCompileEvent extends Event
{
    const NAME = 'huh.news.list.event.before_compile';

    /**
     * @var ModuleNewsList
     */
    protected $module;

    /**
     * @var FrontendTemplate
     */
    protected $template;

    /**
     * NewsListParseEvent constructor.
     *
     * @param ModuleNewsList $module
     */
    public function __construct(ModuleNewsList $module)
    {
        $this->module = $module;
        $this->template = $module->Template;
    }

    /**
     * Get the current news list module object.
     *
     * @return ModuleNewsList
     */
    public function getModule(): ModuleNewsList
    {
        return $this->module;
    }

    /**
     * Get the current template object.
     *
     * @return FrontendTemplate
     */
    public function getTemplate(): FrontendTemplate
    {
        return $this->template;
    }
}

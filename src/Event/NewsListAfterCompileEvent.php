<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\Event;

use Contao\ModuleNewsList;
use Contao\FrontendTemplate;
use Symfony\Component\EventDispatcher\Event;

class NewsListAfterCompileEvent extends Event
{
    const NAME = 'huh.news.list.event.after_compile';

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
     * @param ModuleNewsList $module
     */
    public function __construct(ModuleNewsList $module)
    {
        $this->module   = $module;
        $this->template = $module->Template;
    }

    /**
     * Get the current news list module object
     * @return ModuleNewsList
     */
    public function getModule(): ModuleNewsList
    {
        return $this->module;
    }

    /**
     * Get the current template object
     * @return FrontendTemplate
     */
    public function getTemplate(): FrontendTemplate
    {
        return $this->template;
    }
}
<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Module;

use HeimrichHannot\NewsBundle\Event\NewsListAfterCompileEvent;
use HeimrichHannot\NewsBundle\Event\NewsListBeforeCompileEvent;

class ModuleNewsList extends \Contao\ModuleNewsList
{
    /**
     * Compile the current element.
     */
    protected function compile()
    {
        /**
         * @var \Symfony\Component\EventDispatcher\EventDispatcher
         * @var $event                                             NewsListAfterCompileEvent
         */
        $dispatcher = \System::getContainer()->get('event_dispatcher');

        $event = $dispatcher->dispatch(NewsListBeforeCompileEvent::NAME, new NewsListBeforeCompileEvent($this));
        $this->Template = $event->getTemplate();

        parent::compile();

        $event = $dispatcher->dispatch(NewsListAfterCompileEvent::NAME, new NewsListAfterCompileEvent($this));
        $this->Template = $event->getTemplate();
    }
}

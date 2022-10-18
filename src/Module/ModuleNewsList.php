<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\Module;


use HeimrichHannot\NewsBundle\Event\NewsListAfterCompileEvent;
use HeimrichHannot\NewsBundle\Event\NewsListBeforeCompileEvent;

class ModuleNewsList extends \Contao\ModuleNewsList
{

    /**
     * Compile the current element
     */
    protected function compile()
    {
        /**
         * @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcher
         * @var $event      NewsListAfterCompileEvent
         */
        $dispatcher = \System::getContainer()->get('event_dispatcher');

        $event          = $dispatcher->dispatch(new NewsListBeforeCompileEvent($this), NewsListBeforeCompileEvent::NAME);
        $this->Template = $event->getTemplate();

        parent::compile();

        $event          = $dispatcher->dispatch(new NewsListAfterCompileEvent($this), NewsListAfterCompileEvent::NAME);
        $this->Template = $event->getTemplate();
    }
}
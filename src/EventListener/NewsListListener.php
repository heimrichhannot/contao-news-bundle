<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\EventListener;

use HeimrichHannot\NewsBundle\Event\NewsListAfterCompileEvent;
use HeimrichHannot\NewsBundle\Event\NewsListBeforeCompileEvent;

class NewsListListener
{
    /**
     * Manipulate the news list template before compile() run.
     *
     * @param NewsListBeforeCompileEvent $event
     */
    public function beforeCompile(NewsListBeforeCompileEvent $event)
    {
        if ($event->getModule()->newsListFilterModule > 0) {
            $event->getTemplate()->filter = \Controller::getFrontendModule($event->getModule()->newsListFilterModule);
        }
    }

    /**
     * Manipulate the news list template after compile() run.
     *
     * @param NewsListAfterCompileEvent $event
     */
    public function afterCompile(NewsListAfterCompileEvent $event)
    {
    }
}

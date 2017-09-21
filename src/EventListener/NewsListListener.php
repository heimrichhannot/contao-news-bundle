<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\EventListener;

use HeimrichHannot\NewsBundle\Event\NewsListAfterCompileEvent;
use HeimrichHannot\NewsBundle\Event\NewsListBeforeCompileEvent;
use HeimrichHannot\NewsBundle\Form\NewsFilterForm;
use Symfony\Component\Form\Forms;

class NewsListListener
{
    /**
     * Manipulate the news list template before compile() run
     * @param NewsListBeforeCompileEvent $event
     */
    public function beforeCompile(NewsListBeforeCompileEvent $event)
    {
        if ($event->getModule()->newsListFilterModule > 0) {
            $event->getTemplate()->filter = \Controller::getFrontendModule($event->getModule()->newsListFilterModule);
        }
    }


    /**
     * Manipulate the news list template after compile() run
     * @param NewsListAfterCompileEvent $event
     */
    public function afterCompile(NewsListAfterCompileEvent $event)
    {

    }


}
<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Module;

use Contao\ModuleModel;
use HeimrichHannot\NewsBundle\NewsFilter\NewsFilterModule;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Forms;

class ModuleNewsListFilter extends \Contao\ModuleNews
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var string
     */
    protected $strTemplate = 'mod_newslist_filter';

    protected $filter;

    public function __construct(ModuleModel $objModule, $strColumn = 'main')
    {
        parent::__construct($objModule, $strColumn);

        $this->filter = new NewsFilterModule($objModule);

        \System::getContainer()->get('huh.news.list_filter.module_registry')->add($this->filter);
    }

    /**
     * @return NewsFilterModule
     */
    public function getFilter(): NewsFilterModule
    {
        return $this->filter;
    }

    /**
     * Compile the current element.
     */
    protected function compile()
    {
        $this->filter->buildForm();

        $form = $this->filter->getForm();

        /**
         * @var \Twig_Environment
         */
        $twig = \System::getContainer()->get('twig');

        $this->Template->form = $twig->render(
            '@HeimrichHannotContaoNews/forms/filter_form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}

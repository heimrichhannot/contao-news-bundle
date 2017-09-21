<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\Module;


use Contao\ModuleModel;
use HeimrichHannot\NewsBundle\Form\NewsFilterForm;
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
     * Compile the current element
     */
    protected function compile()
    {
        $this->filter->buildForm();

        $form = $this->filter->getForm();

        /**
         * @var \Twig_Environment $twig
         */
        $twig = \System::getContainer()->get('twig');

        $this->Template->form = $twig->render(
            '@HeimrichHannotContaoNews/forms/filter_form.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @return NewsFilterModule
     */
    public function getFilter(): NewsFilterModule
    {
        return $this->filter;
    }
}
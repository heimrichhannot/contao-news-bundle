<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\NewsFilter;

use Contao\Module;
use Contao\ModuleModel;
use HeimrichHannot\NewsBundle\Form\NewsFilterForm;
use HeimrichHannot\NewsBundle\QueryBuilder\NewsFilterQueryBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;

class NewsFilterModule
{
    /**
     * @var \Contao\Model|ModuleModel|null
     */
    protected $module;

    /**
     * The form builder
     * @var FormBuilderInterface
     */
    protected $formBuilder;


    /**
     * The form instance
     * @var FormInterface
     */
    protected $form;

    public function __construct($module)
    {
        $model = null;

        if ($module instanceof Module) {
            $model = $module->getModel();
        } else if ($module instanceof ModuleModel) {
            $model = $module;
        } else if (is_numeric($module)) {
            $model = ModuleModel::findByPk($module);
        }

        if ($model === null) {
            throw new \InvalidArgumentException(sprintf('The module does not exist'));
        }

        $this->module = $model;
    }

    public function getFilters()
    {
        $filters = deserialize($this->module->newsListFilters, true);

        return array_intersect($filters, \System::getContainer()->get('huh.news.list_filter.registry')->getAliases());
    }

    public function getAlias()
    {
        return $this->module->id;
    }

    /**
     * @return \Contao\Model|ModuleModel|null
     */
    public function getModule()
    {
        return $this->module;
    }

    public function buildForm()
    {
        $factory = Forms::createFormFactoryBuilder()->addExtensions([])->getFormFactory();

        $options = [];

        $builder = $factory->createBuilder(NewsFilterForm::class, ['module' => $this], $options);

        foreach ($this->getFilters() as $alias) {
            /**
             * @var $filter NewsFilterInterface
             */
            $filter = \System::getContainer()->get('huh.news.list_filter.registry')->get($alias);
            $filter->buildForm($builder, $this);
        }

        $this->formBuilder = $builder;
        $this->form        = $builder->getForm()->handleRequest();
    }

    /**
     * @return FormBuilderInterface
     */
    public function getFormBuilder()
    {
        return $this->formBuilder;
    }

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function buildQueries(NewsFilterQueryBuilder $builder, $count = false)
    {
        foreach ($this->getFilters() as $alias) {
            /**
             * @var $filter NewsFilterInterface
             */
            $filter = \System::getContainer()->get('huh.news.list_filter.registry')->get($alias);
            $filter->buildQuery($builder, $this->getForm()->getData(), $count);
        }
    }
}
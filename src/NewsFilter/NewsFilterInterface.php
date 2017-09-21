<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\NewsFilter;

use HeimrichHannot\NewsBundle\QueryBuilder\NewsFilterQueryBuilder;
use Symfony\Component\Form\FormBuilderInterface;

interface NewsFilterInterface
{
    /**
     * Build the filter query
     * @param NewsFilterQueryBuilder $builder The query builder
     * @param array $data The form data
     * @param boolean $count Distinguish between count or fetch query
     */
    public function buildQuery(NewsFilterQueryBuilder $builder, array $data = [], $count = false);

    /**
     * Builds the form, add your filter fields here
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param NewsFilterModule $filter The current filter module
     */
    public function buildForm(FormBuilderInterface $builder, NewsFilterModule $filter);

    /**
     * Clarify the filter name
     * @return string The filter name
     */
    public static function getName();
}
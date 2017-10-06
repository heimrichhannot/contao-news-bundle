<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\NewsFilter\Filter;

use HeimrichHannot\NewsBundle\NewsFilter\NewsFilterInterface;
use HeimrichHannot\NewsBundle\NewsFilter\NewsFilterModule;
use HeimrichHannot\NewsBundle\QueryBuilder\NewsFilterQueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class DatepickerFilter implements NewsFilterInterface
{
    CONST DATEPICKERSTARTNAME = 'datepicker_start';
    CONST DATEPICKERENDNAME   = 'datepicker_end';

    /**
     * Build the filter query
     *
     * @param NewsFilterQueryBuilder $builder The query builder
     * @param boolean                $count   Distinguish between count or fetch query
     */
    public function buildQuery(NewsFilterQueryBuilder $builder, array $data = [], $count = false)
    {
        $start = strtotime($data[DatepickerFilter::DATEPICKERSTARTNAME]->date);
        $end   = strtotime($data[DatepickerFilter::DATEPICKERENDNAME]->date);


        if ($start > 0 && $end > 0) {
            $builder->addColumns(["tl_news.date >= ? AND tl_news.date <= ?"]);
            $builder->addValues([$start, $end]);
        }
    }

    /**
     * Builds the form, add your filter fields here
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param NewsFilterModule     $filter  The current filter module
     */
    public function buildForm(FormBuilderInterface $builder, NewsFilterModule $filter)
    {
        $builder->add(static::DATEPICKERSTARTNAME, DateType::class, [
            'widget'   => 'single_text', // render as a single text box
            'required' => false,
            'html5'    => false,
            'label'    => false,
            'attr'     => [
                'class'       => 'bs_datetimepicker',
                'placeholder' => 'news.form.filter.placeholder.datepicker.start',
            ],
        ]);
        $builder->add(static::DATEPICKERENDNAME, DateType::class, [
            'widget'   => 'single_text', // render as a single text box
            'required' => false,
            'html5'    => false,
            'label'    => false,
            'attr'     => [
                'class'       => 'bs_datetimepicker',
                'placeholder' => 'news.form.filter.placeholder.datepicker.end',
            ],
        ]);
    }

    /**
     * Clarify the filter name
     *
     * @return string The filter name
     */
    public static function getName()
    {
        return 'datepicker';
    }


}
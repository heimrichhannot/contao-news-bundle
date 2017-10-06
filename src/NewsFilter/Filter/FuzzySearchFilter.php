<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\NewsFilter\Filter;

use HeimrichHannot\NewsBundle\Choices\ArchivesChoice;
use HeimrichHannot\NewsBundle\NewsFilter\NewsFilterInterface;
use HeimrichHannot\NewsBundle\NewsFilter\NewsFilterModule;
use HeimrichHannot\NewsBundle\QueryBuilder\NewsFilterQueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FuzzySearchFilter implements NewsFilterInterface
{
    CONST SUBMIT_NAME = 'submit';

    /**
     * Build the filter query
     *
     * @param NewsFilterQueryBuilder $builder The query builder
     * @param boolean                $count   Distinguish between count or fetch query
     */
    public function buildQuery(NewsFilterQueryBuilder $builder, array $data = [], $count = false)
    {
        $searchString = $data[FuzzySearchFilter::getName()];

        if (!empty($searchString)) {
            $builder->addColumns(["(tl_news.headline LIKE ? OR tl_news.teaser LIKE ?)"]);
            $builder->addValues(['%' . $searchString . '%', '%' . $searchString . '%',]);
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
        $builder->add(static::getName(), TextType::class, [
            'required' => false,
            'label'    => false,
            'attr'     => [
                'placeholder' => 'news.form.filter.placeholder.fuzzy',
            ],
        ]);

        $builder->add(static::SUBMIT_NAME, SubmitType::class, [
            'label' => 'news.form.filter.label.fuzzy.submit',
            'attr'  => [
                'class' => 'btn-secondary fuzzy-submit',
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
        return 'fuzzy_string';
    }


}
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
use Symfony\Component\Form\FormBuilderInterface;

class ArchiveFilter implements NewsFilterInterface
{
    /**
     * Build the filter query
     *
     * @param NewsFilterQueryBuilder $builder The query builder
     * @param boolean                $count   Distinguish between count or fetch query
     */
    public function buildQuery(NewsFilterQueryBuilder $builder, array $data = [], $count = false)
    {
        $archiveId = intval($data[ArchiveFilter::getName()]);

        if ($archiveId > 0) {
            $builder->addColumns(["tl_news.pid = ?"]);
            $builder->addValues([$archiveId]);
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
        $builder->add(static::getName(), ChoiceType::class, [
                'choices'     => ArchivesChoice::create($filter, $builder->getData())->getChoices(),
                'required'    => false,
                'placeholder' => 'news.form.filter.placeholder.archive',
                'label'       => false,
                'attr'        => [
                    'onchange' => 'this.form.submit()',
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
        return 'archives';
    }


}
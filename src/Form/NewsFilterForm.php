<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\Form;


use HeimrichHannot\Haste\Util\Url;
use HeimrichHannot\NewsBundle\Choices\MonthChoice;
use HeimrichHannot\NewsBundle\Choices\YearChoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class NewsFilterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');
    }

    public function getBlockPrefix()
    {
        return 'nf';
    }

    /**
     * Generate a filter url with custom search filter parameters
     *
     * @param array $params
     * @param null|string $url
     *
     * @return string
     */
    public static function generateUrl(array $params = [], $url = null)
    {
        $form = new static();

        return rawurldecode(
            Url::addParametersToUri(Url::removeQueryString([$form->getBlockPrefix()], $url), [$form->getBlockPrefix() => $params])
        );
    }
}
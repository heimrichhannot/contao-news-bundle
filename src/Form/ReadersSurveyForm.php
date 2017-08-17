<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 28.07.17
 * Time: 12:16
 */

namespace HeimrichHannot\NewsBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ReadersSurveyForm extends AbstractType
{
    const ANSWERS = 'answers';
    const SUBMIT  = 'submit';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        $builder->setMethod('GET');
        $builder->add(
            static::ANSWERS,
            ChoiceType::class,
            [
                'choices'  => $data['answers'],
                'multiple' => false,
                'expanded' => true,
                'required' => true,
                'attr'     => ['class' => 'readers-survey-answer'],
            ]
        );
        $builder->add(
            static::SUBMIT,
            SubmitType::class,
            [
                'label' => 'news.readers.survey.submit',
                'attr'  => [
//                    'data-action' => $options['action'][static::SUBMIT],
                    'class'       => 'btn btn-primary',
                ],
            ]
        );
    }
}
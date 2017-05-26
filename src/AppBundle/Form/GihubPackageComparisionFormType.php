<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GihubPackageComparisionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add(
                'firstRepoName',
                TextType::class,
                array('attr' => array('class' => 'form-control', 'title' => 'app.first_repo_name', 'placeholder' => 'app.first_repo_name'))
            )
            ->add(
                'secondRepoName',
                TextType::class,
                array('attr' => array('class' => 'form-control', 'placeholder' => 'app.second_repo_name', 'title' => 'app.second_repo_name'))
            )
            ->add(
                'save',
                SubmitType::class,
                array(
                    'label' => 'app.submit_name',
                    'attr' => array('class' => 'btn btn-lg btn-success btn-block', 'label_format' => 'app.submit_name')
                )
            )
         ;
    }
}
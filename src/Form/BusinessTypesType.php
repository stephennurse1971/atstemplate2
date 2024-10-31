<?php

namespace App\Form;

use App\Entity\BusinessTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusinessTypesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ranking')
            ->add('businessType')
            ->add('mapIcon')
            ->add('mapIconColour')
            ->add('mapDisplay')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BusinessTypes::class,
        ]);
    }
}

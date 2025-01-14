<?php

namespace App\Form;

use App\Entity\Languages;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ranking')
            ->add('isActive')
            ->add('language')
            ->add('abbreviation')
            ->add('linkedInOther')
            ->add('icon', FileType::class, [
                'label' => 'Icon',
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Languages::class,
        ]);
    }
}

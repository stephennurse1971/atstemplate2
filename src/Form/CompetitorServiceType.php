<?php

namespace App\Form;

use App\Entity\Competitors;
use App\Entity\CompetitorService;
use App\Entity\ServicesOffered;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompetitorServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('price')
            ->add('competitor', EntityType::class, [
                'class' => Competitors::class,
                'choice_label' => 'name',
                'required' => true,
                'empty_data' => null,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompetitorService::class,
        ]);
    }
}

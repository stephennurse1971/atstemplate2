<?php

namespace App\Form;

use App\Entity\BusinessContacts;
use App\Entity\Referrals;
use App\Entity\User;
use App\Services\TranslationsWorkerService;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReferralsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'required' => true,
                'empty_data' => null,
            ])
            ->add('businessSite', EntityType::class, [
                'class' => BusinessContacts::class,
                'choice_label' => 'company',
                'required' => true,
                'empty_data' => null,
            ])
            ->add('dateTime', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('action', ChoiceType::class, [
                'multiple' => false,
                'placeholder' => false,
                'expanded' => true,
                'required' => false,
                'choices' => [
                    'WebSite' => 'Website',
                    'VCF' => 'VCF',
                    'Call' => 'Call',
                    'Location'=>'Location'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Referrals::class,
        ]);
    }

    public function __construct(TranslationsWorkerService $translationsWorker)
    {
        $this->translationsWorker = $translationsWorker;
    }

}

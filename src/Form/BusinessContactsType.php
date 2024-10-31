<?php

namespace App\Form;

use App\Entity\BusinessContacts;
use App\Entity\BusinessTypes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusinessContactsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Pending' => 'Pending',
                    'Approved' => 'Approved',
                    'Not approved' => 'Not approved'
                ],])
            ->add('publicPrivate', ChoiceType::class, [
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Public' => 'Public',
                    'Private' => 'Private'
                ],])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'required' => false
            ])
            ->add('businessType', EntityType::class, [
                'class' => BusinessTypes::class,
                'choice_label' => 'businessType',
                'required' => true,
                'empty_data' => null,
            ])
            ->add('businessOrPerson', ChoiceType::class, [
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Person' => 'Person',
                    'Business' => 'Business'
                ],])
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('mobile')
            ->add('landline')
            ->add('company')
            ->add('website')
            ->add('addressStreet')
            ->add('addressCity')
            ->add('addressCounty')
            ->add('addressPostCode')
            ->add('addressCountry')
            ->add('locationLongitude')
            ->add('locationLatitude')
            ->add('notes')
            ->add('files', FileType::class, [
                'label' => 'Files',
                'mapped' => false,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BusinessContacts::class,
        ]);
    }
}

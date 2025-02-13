<?php

namespace App\Form;

use App\Entity\Languages;
use App\Entity\User;
use App\Services\TranslationsWorkerService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('salutation', ChoiceType::class, [
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Mr.' => 'Mr.',
                    'Ms.' => 'Ms.',
                    'Mrs.' => 'Mrs.'
                ],])
            ->add('firstName', TextType::class, [
                'required' => false
            ])
            ->add('lastName', TextType::class, [
                'required' => false
            ])
            ->add('emailVerified')
            ->add('jobTitle', TextType::class, [
                'required' => false
            ])
            ->add('defaultLanguage', EntityType::class, [
                'class' => Languages::class,
                'required' => false,
                'choice_label' => 'language'
            ])
            ->add('linkedIn', TextType::class, [
                'required' => false
            ])
            ->add('businessStreet', TextType::class, [
                'required' => false
            ])
            ->add('businessCity', TextType::class, [
                'required' => false
            ])
            ->add('businessPostalCode', TextType::class, [
                'required' => false
            ])
            ->add('businessCountry', TextType::class, [
                'required' => false
            ])
            ->add('homeStreet', TextType::class, [
                'required' => false
            ])
            ->add('homeCity', TextType::class, [
                'required' => false
            ])
            ->add('homePostalCode', TextType::class, [
                'required' => false
            ])
            ->add('homeCountry', TextType::class, [
                'required' => false
            ])
            ->add('email')
            ->add('email2', TextType::class, [
                'required' => false
            ])
            ->add('email3', TextType::class, [
                'required' => false
            ])
            ->add('mobile', TextType::class, [
                'required' => false
            ])
            ->add('mobile2', TextType::class, [
                'required' => false
            ])
            ->add('businessPhone', TextType::class, [
                'required' => false
            ])
            ->add('homePhone', TextType::class, [
                'required' => false
            ])
            ->add('homePhone2', TextType::class, [
                'required' => false
            ])
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('webPage', TextType::class, [
                'required' => false
            ])
            ->add('notes', TextType::class, [
                'required' => false
            ])
            ->add('password', PasswordType::class, [
                'required' => false,
                'empty_data' => ''
            ])

            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'required' => false
            ])
        ;
        $logged_user_roles = $this->security->getUser()->getRoles();

        $user_roles = $options['user']->getRoles();
        if (in_array('ROLE_ADMIN', $logged_user_roles) or in_array('ROLE_SUPER_ADMIN', $logged_user_roles)) {
            $builder
                ->add('roles', ChoiceType::class, [
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => [
                        'Super-Admin' => 'ROLE_SUPER_ADMIN',
                        'Admin' => 'ROLE_ADMIN',
                        'User' => 'ROLE_USER'
                    ],
                    // 'mapped' => false
                ])
                ->add('company', TextType::class, [
                    'required' => false
                ]);
        }
    }

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'email1' => null,
            'email2' => null,
            'user' => null
        ]);
    }
    
}

<?php

namespace App\Form;

use App\Entity\Languages;
use App\Entity\User;
use App\Repository\LanguagesRepository;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class UserType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('salutation', ChoiceType::class, [
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'choices' => [
                    'Mr.' => 'Mr.',
                    'Ms.' => 'Ms.',
                    'Mrs.' => 'Mrs.'
                ],
            ])
            ->add('firstName', TextType::class, [
                'required' => false
            ])
            ->add('lastName', TextType::class, [
                'required' => false
            ])
            ->add('jobTitle', TextType::class, [
                'required' => false
            ])
            ->add('defaultLanguage', EntityType::class, [
                'class' => Languages::class,
                'required' => false,
                'choice_label' => 'language',
                'query_builder' => function (LanguagesRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.ranking', 'ASC');
                }
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
            ->add('company', TextType::class, [
                'required' => false
            ]);

        $loggedUser = $this->security->getUser();
        if ($loggedUser && (in_array('ROLE_ADMIN', $loggedUser->getRoles()) || in_array('ROLE_SUPER_ADMIN', $loggedUser->getRoles()))) {
            $builder
                ->add('emailVerified')
                ->add('roles', ChoiceType::class, [
                    'multiple' => true,
                    'required' => true,
                    'expanded' => true,
                    'choices' => [
                        'Super Admin' => 'ROLE_SUPER_ADMIN',
                        'Admin' => 'ROLE_ADMIN',
                        'User' => 'ROLE_USER'
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'user' => null, // Ensure this is handled in your form controller
        ]);
    }
}

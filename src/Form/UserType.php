<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
                // 'mapped' => false,
            ])
            ->add('sendEmail', HiddenType::class, [
                'mapped' => false,
                'required' => false
            ]);
        $logged_user_roles = $this->security->getUser()->getRoles();

        $user_roles = $options['user']->getRoles();
        if (in_array('ROLE_ADMIN', $logged_user_roles) or in_array('ROLE_SUPER_ADMIN', $logged_user_roles) ) {
            $builder
                ->add('lastEdited', DateType::class, [
                    'required' => false,
                    'widget' => 'single_text'
                ])
                ->add('inviteDate', DateType::class, [
                    'required' => false,
                    'widget' => 'single_text'
                ])

                ->add('roles', ChoiceType::class, [
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => [
                        'Super-Admin' => 'ROLE_SUPER_ADMIN',
                        'Admin' => 'ROLE_ADMIN',
                        'IT' => 'ROLE_IT',
                        'Family' => 'ROLE_FAMILY',
                        'Health' => 'ROLE_HEALTH',
                        'HMRC' => 'ROLE_HMRC',
                        'Accountant' => 'ROLE_ACCOUNTANT',
                        'Contact' => 'ROLE_CONTACT',
                        'Recruiter' => 'ROLE_RECRUITER',
                        'Job applicant' => 'ROLE_JOB_APPLICANT',
                        'Guest' => 'ROLE_GUEST'
                    ],
                    // 'mapped' => false
                ])
                ->add('company', TextType::class, [
                    'required' => false
                ]);
        }

        if (in_array('ROLE_RECRUITER', $user_roles)) {
            $builder
                ->add('recruiterHighPriority')
                ->add('recruitingArea')
                ->add('recruitingAreaList', ChoiceType::class, [
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => [
                        'Asset Management' => 'AM',
                        'Investment Banking' => 'IB',
                        'Fixed Income' => 'FI',
                        'Equities' => 'Eq',
                        'Hedge Funds' => 'HF',
                        'Risk' => 'Risk',
                        'Private Equity' => 'PE',
                        'CEOs' => 'CEOs',
                        'Compliance' => 'Compl',
                        'HR' => 'HR',
                        'Human Resources' => 'Human Resources',
                        'Benefits' => 'Benefits'
                    ],])
                ->add('recruiterResponse', ChoiceType::class, [
                    'multiple' => false,
                    'required' => false,
                    'expanded' => false,
                    'choices' => [
                        'No reply' => 'No reply',
                        'Replied and No' => 'Replied and No',
                        'Replied and Follow-up' => 'Replied and Follow-up'
                    ],
                    'data' => 'No reply',
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

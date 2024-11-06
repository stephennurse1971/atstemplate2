<?php

namespace App\Form;

use App\Entity\Countries;
use App\Entity\User;
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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('emailVerified')
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'required' => false,
                'invalid_message' => 'You entered an invalid value',
            ])
            ->add('firstName')
            ->add('lastName')
            ->add('mobile')
            ->add('roles', ChoiceType::class, [
                    'mapped' => true,
                    'multiple' => true,
                    'placeholder' => '',
                    'required' => false,
                    'choices' => [
                        'Super_Admin' => 'ROLE_SUPER_ADMIN',
                        'Admin' => 'ROLE_ADMIN',
                        'Partner' => 'ROLE_PARTNER',
                        'User' => 'ROLE_USER'
                    ]]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'user' => null
        ]);
    }

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
}

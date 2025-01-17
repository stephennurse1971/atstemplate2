<?php

namespace App\Form;

use App\Entity\EmployeeCalendarSetUp;
use App\Entity\FacebookGroups;
use App\Entity\FacebookGroupsReviews;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class FacebookGroupsReviewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder

            ->add('comment')
            ->add('facebookGroup', EntityType::class, [
                'class' => FacebookGroups::class,
                'choice_label' => 'name',
                'required' => true,
                'empty_data' => null,
                'choices'=>$options['facebookGroup']
            ])
            ->add('reviewer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'choices'=>$this->userRepository->findByRole('ROLE_ADMIN'),
                'required' => true,
                'data' => $this->security->getUser(),
            ]);
        if($options['mode'] == 'new'){
            $builder->add('date', DateType::class, [
                'widget' => 'single_text',
                'data'=> new \DateTime('now')
            ]);
        }
        else{
            $builder->add('date', DateType::class, [
                'widget' => 'single_text',

            ]);
        }
    }
    public function __construct(UserRepository $userRepository,Security $security){
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FacebookGroupsReviews::class,
            'facebookGroup'=>null,
            'mode'=>null
        ]);
    }

}


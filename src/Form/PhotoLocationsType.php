<?php

namespace App\Form;

use App\Entity\PhotoLocations;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\TranslationsWorkerService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhotoLocationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $users = [];
        $users_family = $this->userRepository->findByRole('ROLE_FAMILY');
        $users_guest = $this->userRepository->findByRole('ROLE_GUEST');
        $users_contact = $this->userRepository->findByRole('ROLE_CONTACT');
        $users = array_merge($users_family, $users_guest, $users_contact);
        $users_temp = [];
        foreach ($users as $user) {
            $users_temp[] = $user->getFullName();
        }
        sort($users_temp);
        $sorted_users = [];
        foreach ($users_temp as $user_name) {
            $sorted_users[] = $this->userRepository->findOneBy(['fullName' => $user_name]);
        }
        $builder
            ->add('location')
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date'])
            ->add('publicPrivate', ChoiceType::class, [
                'multiple' => false,
                'required' => false,
                'choices' => [
                    'Public' => 'Public',
                    'Private' => 'Private'
                ]])
            ->add('enabledUsers', EntityType::class, [
                'class' => User::class,
                'choices' => $sorted_users,
                'choice_label' => 'fullName',
                'required' => false,
                'multiple' => true,
                'mapped' => false,
                'data'=>$options['Users']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PhotoLocations::class,
            'Users'=>null
        ]);
    }

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }

//    public function __construct(TranslationsWorkerService $translationsWorker)
//    {
//        $this->translationsWorker = $translationsWorker;
//    }

}

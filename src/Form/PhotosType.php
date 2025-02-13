<?php

namespace App\Form;

use App\Entity\PhotoLocations;
use App\Entity\Photos;
use App\Entity\User;
use App\Services\TranslationsWorkerService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class PhotosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', EntityType::class, [
                'class' => PhotoLocations::class,
                'choice_label' => 'location',
                'required' => false,
                'empty_data' => null,
                'data' => $options['location']
            ])
            ->add('photoFile', FileType::class, [
                'multiple' => true,
                'mapped' => false
            ])

            ->add('favourites', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'required' => false,
                'multiple'=>true
            ])
        ;
        if($options['mode']=='new'){
            $builder->add('uploadedBy', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'choice_label'=>'fullName',
                'data'=>$this->security->getUser()
            ]);
        }
        else{
            $builder->add('uploadedBy', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'choice_label'=>'fullName',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Photos::class,
            'location' => null,
             'mode'=>null
        ]);
    }
    public function __construct(Security $security){
        $this->security = $security;
    }

//    public function __construct(TranslationsWorkerService $translationsWorker)
//    {
//        $this->translationsWorker = $translationsWorker;
//    }

}

<?php

namespace App\Form;

use App\Entity\BusinessContacts;
use App\Entity\BusinessTypes;
use App\Repository\BusinessTypesRepository;
use App\Services\TranslationsWorkerService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusinessContactsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => $this->translationsWorker->getTranslations('Status'),
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Pending' => 'Pending',
                    'Approved' => 'Approved',
                    'Not approved' => 'Not approved'
                ],])

            ->add('photo', FileType::class, [
                'label' => $this->translationsWorker->getTranslations('Logo'),
                'mapped' => false,
                'required' => false
            ])
            ->add('businessType', EntityType::class, [
                'label' => $this->translationsWorker->getTranslations('Business Type'),
                'class' => BusinessTypes::class,
                'choice_label' => 'businessType',
                'required' => true,
                'empty_data' => null,
                'query_builder' => function (BusinessTypesRepository $er) {
                    return $er->createQueryBuilder('bt')
                        ->orderBy('bt.ranking', 'ASC');
                },
            ])
            ->add('businessOrPerson', ChoiceType::class, [
                'label' => $this->translationsWorker->getTranslations('Business Or Person'),
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Person' => 'Person',
                    'Business' => 'Business'
                ],])
            ->add('firstName', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('First Name'),
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Last Name'),
                'required' => false,
            ])
            ->add('email', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Email'),
                'required' => false,
            ])
            ->add('mobile', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Mobile'),
                'required' => false,
            ])
            ->add('landline', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Landline'),
                'required' => false,
            ])
            ->add('company', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Company'),
                'required' => false,
            ])
            ->add('website', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Website'),
                'required' => false,
            ])
            ->add('addressStreet', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Address Street'),
                'required' => false,
            ])
            ->add('addressCity', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Address City'),
                'required' => false,
            ])
            ->add('addressCounty', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Address County'),
                'required' => false,
            ])
            ->add('addressPostCode', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Address Post Code'),
                'required' => false,
            ])
            ->add('addressCountry', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Address Country'),
                'required' => false,
            ])
            ->add('locationLongitude', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Location Longitude'),
                'required' => false,
            ])
            ->add('locationLatitude', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Location Latitude'),
                'required' => false,
            ])
            ->add('notes', TextareaType::class, [
                'label' => $this->translationsWorker->getTranslations('Notes'),
                'required' => false,
            ])
            ->add('files', FileType::class, [
                'label' => $this->translationsWorker->getTranslations('Files'),
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
    public function __construct(TranslationsWorkerService $translationsWorker)
    {
        $this->translationsWorker = $translationsWorker;
    }
}

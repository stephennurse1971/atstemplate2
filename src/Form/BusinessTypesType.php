<?php

namespace App\Form;

use App\Entity\BusinessTypes;
use App\Entity\CmsCopyPageFormats;
use App\Entity\MapIcons;
use App\Repository\BusinessTypesRepository;
use App\Repository\MapIconsRepository;
use App\Services\TranslationsWorkerService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusinessTypesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ranking')
            ->add('businessType')
            ->add('description')
            ->add('mapIcon', EntityType::class, [
                'label' => $this->translationsWorker->getTranslations('Map Icon'),
                'class' => MapIcons::class,
                'required' => true,
                'choice_label' => 'name',
                'query_builder' => function (MapIconsRepository $er) {
                    return $er->createQueryBuilder('bt')
                        ->orderBy('bt.name', 'ASC');
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BusinessTypes::class,
        ]);
    }

    public function __construct(TranslationsWorkerService $translationsWorker)
    {
        $this->translationsWorker = $translationsWorker;
    }
}

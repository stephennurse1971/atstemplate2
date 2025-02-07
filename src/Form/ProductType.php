<?php

namespace App\Form;

use App\Entity\Product;
use App\Services\TranslationsWorkerService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product')
            ->add('category', ChoiceType::class,[
                'required'=>true,
                'choices'=>[
                    'Main'=>'Main',
                    'Sub'=>'Sub'
                ]
            ])
            ->add('ranking')
            ->add('isActive')
            ->add('includeInFooter')
            ->add('includeInContactForm')
            ->add('notes', TextareaType::class,[
                'required'=>false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }

    public function __construct(TranslationsWorkerService $translationsWorker)
    {
        $this->translationsWorker = $translationsWorker;
    }

}

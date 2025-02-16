<?php

namespace App\Form;

use App\Entity\CmsPhoto;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Services\TranslationsWorkerService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CmsPhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', ChoiceType::class, [
                'label' => $this->translationsWorker->getTranslations('Category'),
                'required' => true,
                'choices' => [
                    'Static' => 'Static',
                    'Product or Service' => 'Product or Service',
                ]
            ])
            ->add('staticPageName')
            ->add('rotate', ChoiceType::class, [
                'label' => $this->translationsWorker->getTranslations('Rotate'),
                'multiple' => false,
                'data'=>'0',
                'expanded' => true,
                'choices' => [
                    '0' => '0',
                    '90' => '90',
                    '180' => '180',
                    '270' => '270',
                ],])
            ->add('product', EntityType::class, [
                'label' => $this->translationsWorker->getTranslations('Product'),
                'class' => Product::class,
                'required' => false,
                'choice_label' => 'product',
                'query_builder' => function (ProductRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.category', 'ASC')
                        ->addOrderBy('p.ranking', 'ASC');
                },
            ])
            ->add('photoOrVideo', ChoiceType::class, [
                'label' => $this->translationsWorker->getTranslations('Photo or Video'),
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Photo' => 'Photo',
                    'Video' => 'Video',
                ],])
            ->add('photo', FileType::class, [
                'label' => $this->translationsWorker->getTranslations('Photo'),
                'mapped' => false,
                'required' => false
            ])
            ->add('title', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Title'),
                'required' => false,
            ])
            ->add('link')
            ->add('ranking');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsPhoto::class,
            'allow_extra_fields' => true,
        ]);
    }
    public function __construct(TranslationsWorkerService $translationsWorker)
    {
        $this->translationsWorker = $translationsWorker;
    }
}

<?php

namespace App\Form;

use App\Entity\CmsCopy;
use App\Entity\CmsCopyPageFormats;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Services\TranslationsWorkerService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CmsCopyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Static' => 'Static',
                    'Product or Service' => 'Product or Service',
                ]
            ])
            ->add('staticPageName')

            ->add('pageLayout', EntityType::class, [
                'class' => CmsCopyPageFormats::class,
                'required' => false,
                'choice_label' => 'name'
            ])

            ->add('product', EntityType::class, [
                'class' => Product::class,
                'required' => false,
                'choice_label' => 'product',
                'query_builder' => function (ProductRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.ranking', 'ASC');
                },
            ])
            ->add('tabTitle')
            ->add('tabTitleFR', TextType::class, [
                'required' => false,
                'label' => 'Tab Title (French)'])
            ->add('tabTitleDE', TextType::class, [
                'required' => false,
                'label' => 'Tab Title (German)'])
            ->add('contentTitle', TextType::class, [
                'required' => false,
                'label' => 'Content title (English)'
            ])
            ->add('contentText', TextareaType::class, [
                'required' => false,
                'label' => 'Main Content (English)'
            ])
            ->add('contentTitleFR', TextType::class, [
                'required' => false,
                'label' => 'Content Title (French)'])
            ->add('contentTextFR', TextareaType::class, [
                'required' => false,
                'label' => 'Main Content (French)'
            ])
            ->add('contentTitleDE', TextType::class, [
                'required' => false,
                'label' => 'Content Title (German)'])
            ->add('contentTextDE', TextareaType::class, [
                'required' => false,
                'label' => 'Main Content (German)'
            ])
            ->add('hyperlinks')
            ->add('attachment', FileType::class, [
                'label' => 'Attachment',
                'mapped' => false,
                'required' => false
            ])
            ->add('ranking')
            ->add('pageCountUsers')
            ->add('pageCountAdmin');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsCopy::class,
        ]);
    }
    public function __construct(TranslationsWorkerService $translationsWorker)
    {
        $this->translationsWorker = $translationsWorker;
    }
}

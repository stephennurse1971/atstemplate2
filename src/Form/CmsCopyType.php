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
                'label' => $this->translationsWorker->getTranslations('Category'),
                'required' => true,
                'choices' => [
                    'Static' => 'Static',
                    'Product or Service' => 'Product or Service',
                ]
            ])
            ->add('staticPageName')
            ->add('pageLayout', EntityType::class, [
                'label' => $this->translationsWorker->getTranslations('Page Layout'),
                'class' => CmsCopyPageFormats::class,
                'required' => false,
                'choice_label' => 'name'
            ])
            ->add('product', EntityType::class, [
                'label' => $this->translationsWorker->getTranslations('Product'),
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
                'label' => $this->translationsWorker->getTranslations('Title FR'),
                'required' => false,])

            ->add('tabTitleDE', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Title DE'),
                'required' => false,])

            ->add('contentTitle', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Content Title'),
                'required' => false,
            ])

            ->add('contentText', TextareaType::class, [
                'label' => $this->translationsWorker->getTranslations('Content'),
                'required' => false,
            ])

            ->add('contentTitleFR', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Content Title FR'),
                'required' => false,
            ])

            ->add('contentTextFR', TextareaType::class, [
                'label' => $this->translationsWorker->getTranslations('Content FR'),
                'required' => false,
            ])

            ->add('contentTitleDE', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Content Title DE'),
                'required' => false,
            ])

            ->add('contentTextDE', TextareaType::class, [
                'label' => $this->translationsWorker->getTranslations('Content DE'),
                'required' => false,
            ])

            ->add('hyperlinks')
            ->add('attachment', FileType::class, [
                'label' => $this->translationsWorker->getTranslations('Attachment'),
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

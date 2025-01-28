<?php

namespace App\Form;

use App\Entity\PhotoLocations;
use App\Entity\Product;
use App\Entity\WebsiteContacts;
use App\Repository\ProductRepository;
use App\Services\TranslationsWorkerService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WebsiteContactsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('mobile')
            ->add('notes')
            ->add('dateTime', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'Product', // Adjust to the field to display as the label
                'required' => false,
                'empty_data' => null,
                'query_builder' => function (ProductRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.ranking', 'ASC'); // Replace 'rank' with your field if it's different
                },
            ])
            ->add('status', ChoiceType::class, [
                'multiple' => false,
                'placeholder' => false,
                'expanded' => true,
                'required' => false,
                'choices' => [
                    'Pending' => 'Pending',
                    'Accepted' => 'Accepted',
                    'Spam' => 'Spam',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WebsiteContacts::class,
        ]);
    }

    public function __construct(TranslationsWorkerService $translationsWorker)
    {
        $this->translationsWorker = $translationsWorker;
    }

}

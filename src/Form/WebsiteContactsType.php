<?php

namespace App\Form;

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
use Symfony\Component\Validator\Constraints\Count;

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
            ->add('productsRequested', EntityType::class, [
                'class' => Product::class,
                'multiple' => true,
                'choice_label' => 'product', // Ensure this matches the entity property name
                'required' => false,
                'empty_data' => null,
                'expanded' => true, // Renders as checkboxes
                'by_reference' => false,
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Please select at least one product.',
                    ])
                ],
                'query_builder' => function (ProductRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.includeInContactForm = :includeInContactForm')
                        ->setParameter('includeInContactForm', true)
                        ->orderBy('p.category', 'ASC')
                        ->addOrderBy('p.ranking', 'ASC');
                },
            ])
            ->add('status', ChoiceType::class, [
                'multiple' => false,
                'placeholder' => false,
                'expanded' => true,
                'required' => true,
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

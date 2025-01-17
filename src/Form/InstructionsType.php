<?php

namespace App\Form;

use App\Entity\Instructions;
use App\Services\TranslationsWorkerService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InstructionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('topic', \Symfony\Component\Form\Extension\Core\Type\TextType::class, [
                'required' => false,
                'label' => 'Topic'
            ])
            ->add('summary')
            ->add('photoOrVideo', ChoiceType::class, [
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Photo' => 'Photo',
                    'Video' => 'Video',
                ],])
            ->add('media', FileType::class, [
                'label' => 'Media',
                'mapped' => false,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Instructions::class,
        ]);
    }

    public function __construct(TranslationsWorkerService $translationsWorker)
    {
        $this->translationsWorker = $translationsWorker;
    }

}

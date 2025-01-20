<?php

namespace App\Form;

use App\Entity\Log;
use App\Entity\ProjectSpecific\User;
use App\Services\TranslationsWorkerService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('createdAt',DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date and Time'])
            ->add('eventKey')
            ->add('additionalInfo')
            ->add('user',EntityType::class,[
                'class'=>\App\Entity\User::class,
                'choice_label'=>'fullName'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Log::class,
        ]);
    }

    public function __construct(TranslationsWorkerService $translationsWorker)
    {
        $this->translationsWorker = $translationsWorker;
    }
}

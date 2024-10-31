<?php

namespace App\Form;

use App\Entity\CompanyDetails;
use App\Repository\TranslationRepository;
use App\Services\Languages;
use App\Services\TranslationsWorker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $builder
            ->add('companyName',TextType::class,[
                'label' =>$this->translationsWorker->getTranslations('Company Name'),
            ])
            ->add('contactFirstName',TextType::class,[
                'label' =>$this->translationsWorker->getTranslations('Contact First Name'),
                'required' => false,
            ])
            ->add('contactLastName')
            ->add('companyWebsite')
            ->add('sqlDatabase')
            ->add('databasePassword')
            ->add('faviconLive', FileType::class, [
                'label' => 'Favicon Live',
                'mapped' => false,
                'required' => false
            ])
            ->add('faviconDev', FileType::class, [
                'label' => 'Favicon Dev',
                'mapped' => false,
                'required' => false
            ])
            ->add('companyEmail')
            ->add('companyEmailPassword')
            ->add('companyEmailImportDirectory')
            ->add('companyEmailImportProcessedDirectory')
            ->add('companyTel')
            ->add('companyMobile')
            ->add('companySkype')
            ->add('companyQrCode', FileType::class, [
                'label' => 'QR Code',
                'mapped' => false,
                'required' => false
            ])
            ->add('companyAddressStreet')
            ->add('companyAddressTown')
            ->add('companyAddressCity')
            ->add('companyAddressPostalCode')
            ->add('companyAddressCountry')
            ->add('companyTimeZone', ChoiceType::class, [
                'multiple' => false,
                'expanded' => FALSE,
                'required' => false,
                'choices' => [
                    'SST' => 'SST',
                    'HST' => 'GMT',
                    'AKST' => 'GMT',
                    'PST' => 'PST',
                    'MST' => 'MST',
                    'CST' => 'CST',
                    'EST' => 'EST',
                    'VET' => 'VET',
                    'AST' => 'AST',
                    'NST' => 'NST',
                    'BRT' => 'BRT',
                    'AZOT' => 'AZOT',
                    'GMT' => 'GMT',
                    'CET' => 'CET',
                    'EET' => 'EET',
                    'MSK' => 'MSK',
                    'IRST' => 'IRST',
                    'GST' => 'GST',
                    'AFT' => 'AFT',
                    'IST' => 'IST',
                    'BTT' => 'BTT',
                    'MMT' => 'MMT',
                    'ICT' => 'ICT',
                    'KST' => 'KST',
                    'ACWST' => 'ACWST',
                    'JST' => 'JST',
                    'ACST' => 'ACST',
                    'AEST' => 'AEST',
                    'LHST' => 'LHST',
                    'SBT' => 'SBT',
                    'NZST' => 'NZST'
                ],
            ])
            ->add('currency')
            ->add('weatherLocation')
            ->add('companyAddressMapLink')
            ->add('companyAddressLongitude')
            ->add('companyAddressLatitude')
            ->add('companyAddressInstructions')
            ->add('facebook')
            ->add('twitter')
            ->add('instagram')
            ->add('linkedIn')
            ->add('headerDisplayProducts', CheckboxType::class, [
                'label' => 'Products',
                'required' => false])
            ->add('headerDisplaySubProducts', CheckboxType::class, [
                'label' => 'SubProducts',
                'required' => false])
            ->add('headerDisplayLogin', CheckboxType::class, [
                'label' => 'Login',
                'required' => false])
            ->add('headerDisplayContactDetails', CheckboxType::class, [
                'label' => 'Contact Details',
                'required' => false])
            ->add('headerDisplayPricing', CheckboxType::class, [
                'label' => 'Pricing',
                'required' => false])
            ->add('headerDisplayInstructions', CheckboxType::class, [
                'label' => 'Instructions',
                'required' => false])
            ->add('headerDisplayTandCs', CheckboxType::class, [
                'label' => 'T&Cs',
                'required' => false])
            ->add('headerDisplayBusinessContacts', CheckboxType::class, [
                'label' => 'Business Contacts',
                'required' => false])
            ->add('headerDisplayWeather', CheckboxType::class, [
                'label' => 'Weather',
                'required' => false])
            ->add('footerDisplayContactDetails', CheckboxType::class, [
                'label' => 'Contact Details',
                'required' => false])
            ->add('footerDisplayAddress', CheckboxType::class, [
                'label' => 'Address',
                'required' => false])
            ->add('footerDisplayTelNumbers', CheckboxType::class, [
                'label' => 'Tel Numbers',
                'required' => false])
            ->add('footerDisplaySocialMedia', CheckboxType::class, [
                'label' => 'Social Media',
                'required' => false])
            ->add('footerDisplayProducts', CheckboxType::class, [
                'label' => 'Products',
                'required' => false])
            ->add('footerDisplaySubProducts', CheckboxType::class, [
                'label' => 'SubProducts',
                'required' => false])
            ->add('homePagePhotosOnly', CheckboxType::class, [
                'label' => 'Photos Only',
                'required' => false])
            ->add('includeContactFormHomePage', CheckboxType::class, [
                'label' => 'Include Contact Form',
                'required' => false])
            ->add('includeQRCodeHomePage', CheckboxType::class, [
                'label' => 'Include QR Code',
                'required' => false])
            ->add('multiLingual', CheckboxType::class, [
                'label' => 'Multi Lingual Site',
                'required' => false])
            ->add('titleProducts')
            ->add('titleSubProducts')
            ->add('enableUserRegistration')
            ->add('registrationEmail')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompanyDetails::class,
        ]);
    }
    public function __construct(TranslationsWorker $translationsWorker)
    {
      $this->translationsWorker = $translationsWorker;
    }
}

<?php

namespace App\Form;

use App\Entity\CompanyDetails;
use App\Repository\TranslationRepository;
use App\Services\LanguagesService;
use App\Services\TranslationsWorkerService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Company Name'),
            ])
            ->add('contactFirstName', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Contact First Name'),
                'required' => false,
            ])
            ->add('contactLastName', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Contact Last Name'),
                'required' => false,
            ])
            ->add('companyWebsite', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Company Website'),
                'required' => false,
            ])
            ->add('sqlDatabase', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Database'),
                'required' => false,
            ])
            ->add('databasePassword', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Password'),
                'required' => false,
            ])
            ->add('faviconLive', FileType::class, [
                'label' => $this->translationsWorker->getTranslations('Favicon Live'),
                'mapped' => false,
                'required' => false
            ])
            ->add('faviconDev', FileType::class, [
                'label' => $this->translationsWorker->getTranslations('Favicon Dev'),
                'mapped' => false,
                'required' => false
            ])
            ->add('companyEmail', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Email'),
                'required' => false,
            ])
            ->add('companyEmailPassword', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Password'),
                'required' => false,
            ])
            ->add('companyEmailImportDirectory', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Import Directory'),
                'required' => false,
            ])
            ->add('companyEmailImportProcessedDirectory', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Import Processed Directory'),
                'required' => false,
            ])
            ->add('companyTel', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Telephone'),
                'required' => false,
            ])
            ->add('companyMobile', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Mobile'),
                'required' => false,
            ])
            ->add('companySkype', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Skype'),
                'required' => false,
            ])
            ->add('companyQrCode', FileType::class, [
                'label' => $this->translationsWorker->getTranslations('QR Code'),
                'mapped' => false,
                'required' => false
            ])
            ->add('companyAddressStreet', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Street'),
                'required' => false,
            ])
            ->add('companyAddressTown', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Town'),
                'required' => false,
            ])
            ->add('companyAddressCity', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('City'),
                'required' => false,
            ])
            ->add('companyAddressPostalCode', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Post Code'),
                'required' => false,
            ])
            ->add('companyAddressCountry', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Country'),
                'required' => false,
            ])
            ->add('companyTimeZone', ChoiceType::class, [
                'label' => $this->translationsWorker->getTranslations('Time Zone'),
                'multiple' => false,
                'expanded' => FALSE,
                'required' => false,
                'choices' => [
                    'SST' => 'SST',
                    'HST' => 'HST',
                    'AKST' => 'AKST',
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
            ->add('currency', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Currency'),
                'required' => false,
            ])
            ->add('weatherLocation', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Weather Location'),
                'required' => false,
            ])
            ->add('companyAddressMapLink', TextareaType::class, [
                'label' => $this->translationsWorker->getTranslations('Map Link'),
                'required' => false,
            ])
            ->add('companyAddressLongitude', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Longitude'),
                'required' => false,
            ])
            ->add('companyAddressLatitude', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Latitude'),
                'required' => false,
            ])
            ->add('companyAddressInstructions', TextareaType::class, [
                'label' => $this->translationsWorker->getTranslations('Instructions'),
                'required' => false,
            ])
            ->add('registrationEmail', TextareaType::class, [
                'label' => $this->translationsWorker->getTranslations('Registration Email'),
                'required' => false,
            ])
            ->add('facebook', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Facebook'),
                'required' => false,
            ])
            ->add('twitter', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Twitter'),
                'required' => false,
            ])
            ->add('instagram', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Instagram'),
                'required' => false,
            ])
            ->add('linkedIn', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('LinkedIn'),
                'required' => false,
            ])
            ->add('websiteContactsEmailAlert', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Email alert'),
                'required' => false])
            ->add('websiteContactsAutoReply', ChoiceType::class, [
                'label' => $this->translationsWorker->getTranslations('Automatic replies'),
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'choices' => [
                    'Auto' => 'Auto',
                    'Semi-auto' => 'Semi-auto',
                    'Manual' => 'Manual',
                ],
            ])
            ->add('headerDisplayProducts', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Products'),
                'required' => false])
            ->add('headerDisplaySubProducts', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Sub Products'),
                'required' => false])
            ->add('headerDisplayPhotos', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Photos'),
                'required' => false])
            ->add('headerDisplayLogin', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Login'),
                'required' => false])
            ->add('headerDisplayContactDetails', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Contact Details'),
                'required' => false])
            ->add('headerDisplayPricing', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Pricing'),
                'required' => false])
            ->add('headerDisplayInstructions', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Instructions'),
                'required' => false])
            ->add('headerDisplayTandCs', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('T&Cs'),
                'required' => false])
            ->add('headerDisplayBusinessContacts', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Business Contacts'),
                'required' => false])
            ->add('headerDisplayFacebookPages', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Facebook Pages'),
                'required' => false])
            ->add('facebookReviewsHistoryShowMonths', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('History (months)'),
                'required' => false,
            ])
            ->add('headerDisplayCompetitors', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Competitors'),
                'required' => false])
            ->add('headerDisplayWeather', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Weather'),
                'required' => false])
            ->add('footerDisplayContactDetails', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Contact Details'),
                'required' => false])
            ->add('footerDisplayAddress', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Address'),
                'required' => false])
            ->add('footerDisplayTelNumbers', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Tel numbers'),
                'required' => false])
            ->add('footerDisplaySocialMedia', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Social Media'),
                'required' => false])
            ->add('footerDisplayProducts', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Products'),
                'required' => false])
            ->add('footerDisplaySubProducts', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Sub Products'),
                'required' => false])
            ->add('homePagePhotosOnly', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Photos only'),
                'required' => false])
            ->add('includeContactFormHomePage', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Include Contact Form'),
                'required' => false])
            ->add('includeQRCodeHomePage', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Include QR Code'),
                'required' => false])
            ->add('multiLingual', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Multi-lingual site'),
                'required' => false])
            ->add('titleProducts', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Products'),
                'required'=>false
            ])
            ->add('titleSubProducts', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Sub-Products'),
                'required'=>false
            ])
            ->add('titleUsefulLinks', TextType::class, [
                'label' => $this->translationsWorker->getTranslations('Useful Links'),
                'required'=>false
            ])
            ->add('enableUserRegistration', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Enable Registration'),
                'required' => false])

            ->add('userIncludeHomeAddress', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Home Address'),
                'required' => false])

            ->add('userIncludeBusinessAddress', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Business Address'),
                'required' => false])

            ->add('userIncludePersonalDetails', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Personal Details'),
                'required' => false])

            ->add('userIncludeJobDetails', CheckboxType::class, [
                'label' => $this->translationsWorker->getTranslations('Job Details'),
                'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompanyDetails::class,
        ]);
    }

    public function __construct(TranslationsWorkerService $translationsWorker)
    {
        $this->translationsWorker = $translationsWorker;
    }
}

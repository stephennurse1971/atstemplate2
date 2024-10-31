<?php

namespace App\Entity;


use App\Repository\CompanyDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompanyDetailsRepository::class)
 */
class CompanyDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyName;



    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyEmail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyTel;



    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyMobile;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyAddressStreet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyAddressTown;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyAddressCity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyAddressPostalCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyAddressCountry;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $companyAddressInstructions;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebook;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkedIn;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $instagram;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $faviconLive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $faviconDev;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $currency;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyEmailPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyEmailImportDirectory;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyEmailImportProcessedDirectory;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $weatherLocation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyWebsite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyAddressLongitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyAddressLatitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyTimeZone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companySkype;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companyQrCode;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $footerDisplayProducts;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $footerDisplaySocialMedia;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $footerDisplayAddress;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $footerDisplayTelNumbers;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $footerDisplayContactDetails;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $headerDisplayLogin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contactFirstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $contactLastName;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $homePagePhotosOnly;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $databasePassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sqlDatabase;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $headerDisplayTandCs;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $headerDisplayPricing;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $headerDisplayInstructions;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titleProducts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titleSubProducts;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $headerDisplayProducts;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $headerDisplaySubProducts;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $footerDisplaySubProducts;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $includeContactFormHomePage;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $multiLingual;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enableUserRegistration;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $registrationEmail;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $includeQRCodeHomePage;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $companyAddressMapLink;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $headerDisplayContactDetails;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $headerDisplayBusinessContacts;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $headerDisplayWeather;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }


    public function getCompanyEmail(): ?string
    {
        return $this->companyEmail;
    }

    public function setCompanyEmail(?string $companyEmail): self
    {
        $this->companyEmail = $companyEmail;

        return $this;
    }


    public function getCompanyTel(): ?string
    {
        return $this->companyTel;
    }

    public function setCompanyTel(string $companyTel): self
    {
        $this->companyTel = $companyTel;

        return $this;
    }

    public function getCompanyMobile(): ?string
    {
        return $this->companyMobile;
    }

    public function setCompanyMobile(?string $companyMobile): self
    {
        $this->companyMobile = $companyMobile;

        return $this;
    }



    public function getCompanyAddressStreet(): ?string
    {
        return $this->companyAddressStreet;
    }

    public function setCompanyAddressStreet(?string $companyAddressStreet): self
    {
        $this->companyAddressStreet = $companyAddressStreet;

        return $this;
    }

    public function getCompanyAddressTown(): ?string
    {
        return $this->companyAddressTown;
    }

    public function setCompanyAddressTown(?string $companyAddressTown): self
    {
        $this->companyAddressTown = $companyAddressTown;

        return $this;
    }



    public function getCompanyAddressCity(): ?string
    {
        return $this->companyAddressCity;
    }

    public function setCompanyAddressCity(?string $companyAddressCity): self
    {
        $this->companyAddressCity = $companyAddressCity;

        return $this;
    }

    public function getCompanyAddressPostalCode(): ?string
    {
        return $this->companyAddressPostalCode;
    }

    public function setCompanyAddressPostalCode(?string $companyAddressPostalCode): self
    {
        $this->companyAddressPostalCode = $companyAddressPostalCode;

        return $this;
    }

    public function getCompanyAddressCountry(): ?string
    {
        return $this->companyAddressCountry;
    }

    public function setCompanyAddressCountry(?string $companyAddressCountry): self
    {
        $this->companyAddressCountry = $companyAddressCountry;

        return $this;
    }





    public function getcompanyAddressInstructions(): ?string
    {
        return $this->companyAddressInstructions;
    }

    public function setCompanyAddressInstructions(?string $companyAddressInstructions): self
    {
        $this->companyAddressInstructions = $companyAddressInstructions;

        return $this;
    }



    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getLinkedIn(): ?string
    {
        return $this->linkedIn;
    }

    public function setLinkedIn(?string $linkedIn): self
    {
        $this->linkedIn = $linkedIn;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getFaviconLive(): ?string
    {
        return $this->faviconLive;
    }

    public function setFaviconLive(?string $faviconLive): self
    {
        $this->faviconLive = $faviconLive;

        return $this;
    }

    public function getFaviconDev(): ?string
    {
        return $this->faviconDev;
    }

    public function setFaviconDev(?string $faviconDev): self
    {
        $this->faviconDev = $faviconDev;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCompanyEmailPassword(): ?string
    {
        return $this->companyEmailPassword;
    }

    public function setCompanyEmailPassword(?string $companyEmailPassword): self
    {
        $this->companyEmailPassword = $companyEmailPassword;

        return $this;
    }

    public function getCompanyEmailImportDirectory(): ?string
    {
        return $this->companyEmailImportDirectory;
    }

    public function setCompanyEmailImportDirectory(?string $companyEmailImportDirectory): self
    {
        $this->companyEmailImportDirectory = $companyEmailImportDirectory;

        return $this;
    }

    public function getCompanyEmailImportProcessedDirectory(): ?string
    {
        return $this->companyEmailImportProcessedDirectory;
    }

    public function setCompanyEmailImportProcessedDirectory(?string $companyEmailImportProcessedDirectory): self
    {
        $this->companyEmailImportProcessedDirectory = $companyEmailImportProcessedDirectory;

        return $this;
    }

    public function getWeatherLocation(): ?string
    {
        return $this->weatherLocation;
    }

    public function setWeatherLocation(?string $weatherLocation): self
    {
        $this->weatherLocation = $weatherLocation;

        return $this;
    }

    public function getCompanyWebsite(): ?string
    {
        return $this->companyWebsite;
    }

    public function setCompanyWebsite(?string $companyWebsite): self
    {
        $this->companyWebsite = $companyWebsite;

        return $this;
    }

    public function getCompanyAddressLongitude(): ?string
    {
        return $this->companyAddressLongitude;
    }

    public function setCompanyAddressLongitude(?string $companyAddressLongitude): self
    {
        $this->companyAddressLongitude = $companyAddressLongitude;

        return $this;
    }

    public function getCompanyAddressLatitude(): ?string
    {
        return $this->companyAddressLatitude;
    }

    public function setCompanyAddressLatitude(?string $companyAddressLatitude): self
    {
        $this->companyAddressLatitude = $companyAddressLatitude;

        return $this;
    }

    public function getCompanyTimeZone(): ?string
    {
        return $this->companyTimeZone;
    }

    public function setCompanyTimeZone(?string $companyTimeZone): self
    {
        $this->companyTimeZone = $companyTimeZone;

        return $this;
    }

    public function getCompanySkype(): ?string
    {
        return $this->companySkype;
    }

    public function setCompanySkype(?string $companySkype): self
    {
        $this->companySkype = $companySkype;

        return $this;
    }

    public function getCompanyQrCode(): ?string
    {
        return $this->companyQrCode;
    }

    public function setCompanyQrCode(?string $companyQrCode): self
    {
        $this->companyQrCode = $companyQrCode;

        return $this;
    }

    public function isFooterDisplayProducts(): ?bool
    {
        return $this->footerDisplayProducts;
    }

    public function setFooterDisplayProducts(?bool $footerDisplayProducts): self
    {
        $this->footerDisplayProducts = $footerDisplayProducts;

        return $this;
    }

    public function isFooterDisplaySocialMedia(): ?bool
    {
        return $this->footerDisplaySocialMedia;
    }

    public function setFooterDisplaySocialMedia(?bool $footerDisplaySocialMedia): self
    {
        $this->footerDisplaySocialMedia = $footerDisplaySocialMedia;

        return $this;
    }

    public function isFooterDisplayAddress(): ?bool
    {
        return $this->footerDisplayAddress;
    }

    public function setFooterDisplayAddress(?bool $footerDisplayAddress): self
    {
        $this->footerDisplayAddress = $footerDisplayAddress;

        return $this;
    }

    public function isFooterDisplayTelNumbers(): ?bool
    {
        return $this->footerDisplayTelNumbers;
    }

    public function setFooterDisplayTelNumbers(?bool $footerDisplayTelNumbers): self
    {
        $this->footerDisplayTelNumbers = $footerDisplayTelNumbers;

        return $this;
    }

    public function isFooterDisplayContactDetails(): ?bool
    {
        return $this->footerDisplayContactDetails;
    }

    public function setFooterDisplayContactDetails(?bool $footerDisplayContactDetails): self
    {
        $this->footerDisplayContactDetails = $footerDisplayContactDetails;

        return $this;
    }

    public function isHeaderDisplayLogin(): ?bool
    {
        return $this->headerDisplayLogin;
    }

    public function setHeaderDisplayLogin(?bool $headerDisplayLogin): self
    {
        $this->headerDisplayLogin = $headerDisplayLogin;

        return $this;
    }

    public function getContactFirstName(): ?string
    {
        return $this->contactFirstName;
    }

    public function setContactFirstName(?string $contactFirstName): self
    {
        $this->contactFirstName = $contactFirstName;

        return $this;
    }

    public function getContactLastName(): ?string
    {
        return $this->contactLastName;
    }

    public function setContactLastName(?string $contactLastName): self
    {
        $this->contactLastName = $contactLastName;

        return $this;
    }

    public function isHomePagePhotosOnly(): ?bool
    {
        return $this->homePagePhotosOnly;
    }

    public function setHomePagePhotosOnly(?bool $homePagePhotosOnly): self
    {
        $this->homePagePhotosOnly = $homePagePhotosOnly;

        return $this;
    }

    public function getDatabasePassword(): ?string
    {
        return $this->databasePassword;
    }

    public function setDatabasePassword(?string $databasePassword): self
    {
        $this->databasePassword = $databasePassword;

        return $this;
    }

    public function getSqlDatabase(): ?string
    {
        return $this->sqlDatabase;
    }

    public function setSqlDatabase(?string $sqlDatabase): self
    {
        $this->sqlDatabase = $sqlDatabase;

        return $this;
    }

    public function getHeaderDisplayTandCs(): ?bool
    {
        return $this->headerDisplayTandCs;
    }

    public function setHeaderDisplayTandCs(?bool $headerDisplayTandCs): self
    {
        $this->headerDisplayTandCs = $headerDisplayTandCs;

        return $this;
    }

    public function getHeaderDisplayPricing(): ?bool
    {
        return $this->headerDisplayPricing;
    }

    public function setHeaderDisplayPricing(?bool $headerDisplayPricing): self
    {
        $this->headerDisplayPricing = $headerDisplayPricing;

        return $this;
    }

    public function getHeaderDisplayInstructions(): ?bool
    {
        return $this->headerDisplayInstructions;
    }

    public function setHeaderDisplayInstructions(?bool $headerDisplayInstructions): self
    {
        $this->headerDisplayInstructions = $headerDisplayInstructions;

        return $this;
    }

    public function getTitleProducts(): ?string
    {
        return $this->titleProducts;
    }

    public function setTitleProducts(?string $titleProducts): self
    {
        $this->titleProducts = $titleProducts;

        return $this;
    }

    public function getTitleSubProducts(): ?string
    {
        return $this->titleSubProducts;
    }

    public function setTitleSubProducts(?string $titleSubProducts): self
    {
        $this->titleSubProducts = $titleSubProducts;

        return $this;
    }

    public function isHeaderDisplayProducts(): ?bool
    {
        return $this->headerDisplayProducts;
    }

    public function setHeaderDisplayProducts(?bool $headerDisplayProducts): self
    {
        $this->headerDisplayProducts = $headerDisplayProducts;

        return $this;
    }

    public function isHeaderDisplaySubProducts(): ?bool
    {
        return $this->headerDisplaySubProducts;
    }

    public function setHeaderDisplaySubProducts(?bool $headerDisplaySubProducts): self
    {
        $this->headerDisplaySubProducts = $headerDisplaySubProducts;

        return $this;
    }

    public function isFooterDisplaySubProducts(): ?bool
    {
        return $this->footerDisplaySubProducts;
    }

    public function setFooterDisplaySubProducts(?bool $footerDisplaySubProducts): self
    {
        $this->footerDisplaySubProducts = $footerDisplaySubProducts;

        return $this;
    }

    public function isIncludeContactFormHomePage(): ?bool
    {
        return $this->includeContactFormHomePage;
    }

    public function setIncludeContactFormHomePage(?bool $includeContactFormHomePage): self
    {
        $this->includeContactFormHomePage = $includeContactFormHomePage;

        return $this;
    }

    public function isMultiLingual(): ?bool
    {
        return $this->multiLingual;
    }

    public function setMultiLingual(?bool $multiLingual): self
    {
        $this->multiLingual = $multiLingual;

        return $this;
    }

    public function isEnableUserRegistration(): ?bool
    {
        return $this->enableUserRegistration;
    }

    public function setEnableUserRegistration(?bool $enableUserRegistration): self
    {
        $this->enableUserRegistration = $enableUserRegistration;

        return $this;
    }

    public function getRegistrationEmail(): ?string
    {
        return $this->registrationEmail;
    }

    public function setRegistrationEmail(?string $registrationEmail): self
    {
        $this->registrationEmail = $registrationEmail;

        return $this;
    }

    public function isIncludeQRCodeHomePage(): ?bool
    {
        return $this->includeQRCodeHomePage;
    }

    public function setIncludeQRCodeHomePage(?bool $includeQRCodeHomePage): self
    {
        $this->includeQRCodeHomePage = $includeQRCodeHomePage;

        return $this;
    }

    public function getCompanyAddressMapLink(): ?string
    {
        return $this->companyAddressMapLink;
    }

    public function setCompanyAddressMapLink(?string $companyAddressMapLink): self
    {
        $this->companyAddressMapLink = $companyAddressMapLink;

        return $this;
    }

    public function isHeaderDisplayContactDetails(): ?bool
    {
        return $this->headerDisplayContactDetails;
    }

    public function setHeaderDisplayContactDetails(?bool $headerDisplayContactDetails): self
    {
        $this->headerDisplayContactDetails = $headerDisplayContactDetails;

        return $this;
    }

    public function isHeaderDisplayBusinessContacts(): ?bool
    {
        return $this->headerDisplayBusinessContacts;
    }

    public function setHeaderDisplayBusinessContacts(?bool $headerDisplayBusinessContacts): self
    {
        $this->headerDisplayBusinessContacts = $headerDisplayBusinessContacts;

        return $this;
    }

    public function isHeaderDisplayWeather(): ?bool
    {
        return $this->headerDisplayWeather;
    }

    public function setHeaderDisplayWeather(?bool $headerDisplayWeather): self
    {
        $this->headerDisplayWeather = $headerDisplayWeather;

        return $this;
    }



}

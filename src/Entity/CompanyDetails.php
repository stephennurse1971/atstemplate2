<?php

namespace App\Entity;


use App\Repository\CompanyDetailsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyDetailsRepository::class)]
#[ORM\Table(name: "company_details")]

class CompanyDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyName;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyEmail;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyTel;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyMobile;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyAddressStreet;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyAddressTown;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyAddressCity;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyAddressPostalCode;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyAddressCountry;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $facebook;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $linkedIn;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $instagram;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $twitter;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $faviconLive;

    public function __construct()
    {
        $this->faviconLive = ''; // or an appropriate default value
    }

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $faviconDev =null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $currency;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyEmailPassword;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyEmailImportDirectory;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyEmailImportProcessedDirectory;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $weatherLocation;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyWebsite;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyAddressLongitude;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyAddressLatitude;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyTimeZone;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companySkype;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $companyQrCode=null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $footerDisplayProducts;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $footerDisplaySocialMedia;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $footerDisplayAddress;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $footerDisplayTelNumbers;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $footerDisplayContactDetails;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $headerDisplayLogin;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $contactFirstName;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $contactLastName;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $homePagePhotosOnly;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $databasePassword;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $sqlDatabase;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $headerDisplayTandCs;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $headerDisplayPricing;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $headerDisplayInstructions;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $headerDisplayProducts;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $headerDisplaySubProducts;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $footerDisplaySubProducts;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $includeContactFormHomePage;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $multiLingual;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $enableUserRegistration;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $includeQRCodeHomePage;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $headerDisplayContactDetails;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $headerDisplayBusinessContacts;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $headerDisplayWeather;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $headerDisplayFacebookPages;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $headerDisplayCompetitors;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $facebookReviewsHistoryShowMonths;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $titleUsefulLinks;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $headerDisplayPhotos;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $titleProducts;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $titleSubProducts;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $companyAddressMapLink;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $companyAddressInstructions;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $registrationEmail;

    #[ORM\Column(nullable: true)]
    private ?bool $userIncludeHomeAddress = null;

    #[ORM\Column(nullable: true)]
    private ?bool $userIncludeBusinessAddress = null;

    #[ORM\Column(nullable: true)]
    private ?bool $userIncludePersonalDetails = null;

    #[ORM\Column(nullable: true)]
    private ?bool $userIncludeJobDetails = null;



    #[ORM\Column(nullable: true)]
    private ?bool $websiteContactsEmailAlert = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $websiteContactsAutoReply = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contactFromResponse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $defaultUserPassword = null;



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

    public function setCompanyTel(?string $companyTel): self
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

    public function getCompanyDetails(): ?CompanyDetails
    {
        return $this->companyDetails;
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

    public function isIncludeQRCodeHomePage(): ?bool
    {
        return $this->includeQRCodeHomePage;
    }

    public function setIncludeQRCodeHomePage(?bool $includeQRCodeHomePage): self
    {
        $this->includeQRCodeHomePage = $includeQRCodeHomePage;
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

    public function isHeaderDisplayFacebookPages(): ?bool
    {
        return $this->headerDisplayFacebookPages;
    }

    public function setHeaderDisplayFacebookPages(?bool $headerDisplayFacebookPages): self
    {
        $this->headerDisplayFacebookPages = $headerDisplayFacebookPages;
        return $this;
    }

    public function isHeaderDisplayCompetitors(): ?bool
    {
        return $this->headerDisplayCompetitors;
    }

    public function setHeaderDisplayCompetitors(?bool $headerDisplayCompetitors): self
    {
        $this->headerDisplayCompetitors = $headerDisplayCompetitors;
        return $this;
    }

    public function getFacebookReviewsHistoryShowMonths(): ?int
    {
        return $this->facebookReviewsHistoryShowMonths;
    }

    public function setFacebookReviewsHistoryShowMonths(?int $facebookReviewsHistoryShowMonths): self
    {
        $this->facebookReviewsHistoryShowMonths = $facebookReviewsHistoryShowMonths;
        return $this;
    }

    public function getTitleUsefulLinks(): ?string
    {
        return $this->titleUsefulLinks;
    }

    public function setTitleUsefulLinks(?string $titleUsefulLinks): self
    {
        $this->titleUsefulLinks = $titleUsefulLinks;
        return $this;
    }

    public function isHeaderDisplayPhotos(): ?bool
    {
        return $this->headerDisplayPhotos;
    }

    public function setHeaderDisplayPhotos(?bool $headerDisplayPhotos): self
    {
        $this->headerDisplayPhotos = $headerDisplayPhotos;
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

    public function getCompanyAddressMapLink(): ?string
    {
        return $this->companyAddressMapLink;
    }

    public function setCompanyAddressMapLink(?string $companyAddressMapLink): self
    {
        $this->companyAddressMapLink = $companyAddressMapLink;
        return $this;
    }

    public function getCompanyAddressInstructions(): ?string
    {
        return $this->companyAddressInstructions;
    }

    public function setCompanyAddressInstructions(?string $companyAddressInstructions): self
    {
        $this->companyAddressInstructions = $companyAddressInstructions;
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

    public function isUserIncludeHomeAddress(): ?bool
    {
        return $this->userIncludeHomeAddress;
    }

    public function setUserIncludeHomeAddress(?bool $userIncludeHomeAddress): static
    {
        $this->userIncludeHomeAddress = $userIncludeHomeAddress;

        return $this;
    }

    public function isUserIncludeBusinessAddress(): ?bool
    {
        return $this->userIncludeBusinessAddress;
    }

    public function setUserIncludeBusinessAddress(?bool $userIncludeBusinessAddress): static
    {
        $this->userIncludeBusinessAddress = $userIncludeBusinessAddress;

        return $this;
    }

    public function isUserIncludePersonalDetails(): ?bool
    {
        return $this->userIncludePersonalDetails;
    }

    public function setUserIncludePersonalDetails(?bool $userIncludePersonalDetails): static
    {
        $this->userIncludePersonalDetails = $userIncludePersonalDetails;

        return $this;
    }

    public function isUserIncludeJobDetails(): ?bool
    {
        return $this->userIncludeJobDetails;
    }

    public function setUserIncludeJobDetails(?bool $userIncludeJobDetails): static
    {
        $this->userIncludeJobDetails = $userIncludeJobDetails;

        return $this;
    }



    public function isWebsiteContactsEmailAlert(): ?bool
    {
        return $this->websiteContactsEmailAlert;
    }

    public function setWebsiteContactsEmailAlert(?bool $websiteContactsEmailAlert): static
    {
        $this->websiteContactsEmailAlert = $websiteContactsEmailAlert;

        return $this;
    }

    public function getWebsiteContactsAutoReply(): ?string
    {
        return $this->websiteContactsAutoReply;
    }

    public function setWebsiteContactsAutoReply(?string $websiteContactsAutoReply): static
    {
        $this->websiteContactsAutoReply = $websiteContactsAutoReply;

        return $this;
    }

    public function getContactFromResponse(): ?string
    {
        return $this->contactFromResponse;
    }

    public function setContactFromResponse(?string $contactFromResponse): static
    {
        $this->contactFromResponse = $contactFromResponse;

        return $this;
    }

    public function getDefaultUserPassword(): ?string
    {
        return $this->defaultUserPassword;
    }

    public function setDefaultUserPassword(?string $defaultUserPassword): static
    {
        $this->defaultUserPassword = $defaultUserPassword;

        return $this;
    }


}

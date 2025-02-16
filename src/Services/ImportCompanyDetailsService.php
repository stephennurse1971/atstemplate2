<?php


namespace App\Services;

use App\Entity\CompanyDetails;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportCompanyDetailsService
{
    public function importCompanyDetails(string $fileName)
    {
        $filepath = $this->container->getParameter('company_details_import_directory');
        $fullpath = $filepath . $fileName;
        $alldataFromCsv = [];
        $row = 0;
        if (($handle = fopen($fullpath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                if ($row == 0) {
                    $row++;
                    continue;
                }
                $num = count($data);
                $row++;
                for ($c = 0; $c < $num; $c++) {
                    $alldataFromCsv[$row][] = $data[$c];
                }
            }
            fclose($handle);
        }
        foreach ($alldataFromCsv as $oneLineFromCsv) {
            $entity = trim($oneLineFromCsv[0]);
            $companyName = trim($oneLineFromCsv[1]);
            $companyWebsite = trim($oneLineFromCsv[2]);
            $contactFirstName = trim($oneLineFromCsv[3]);
            $contactLastName = trim($oneLineFromCsv[4]);
            $companyTel = trim($oneLineFromCsv[5]);
            $companyMobile = trim($oneLineFromCsv[6]);
            $companySkype = trim($oneLineFromCsv[7]);
            $faviconLive = trim($oneLineFromCsv[8]);
            $faviconDev = trim($oneLineFromCsv[9]);
            $companyQrCode = trim($oneLineFromCsv[10]);
            $companyEmail = trim($oneLineFromCsv[11]);
            $companyEmailPassword = trim($oneLineFromCsv[12]);
            $companyEmailImportDirectory = trim($oneLineFromCsv[13]);
            $companyEmailImportProcessedDirectory = trim($oneLineFromCsv[14]);
            $sqlDatabase = trim($oneLineFromCsv[15]);
            $databasePassword = trim($oneLineFromCsv[16]);
            $companyAddressStreet = trim($oneLineFromCsv[17]);
            $companyAddressTown = trim($oneLineFromCsv[18]);
            $companyAddressCity = trim($oneLineFromCsv[19]);
            $companyAddressPostalCode = trim($oneLineFromCsv[20]);
            $companyAddressCountry = trim($oneLineFromCsv[21]);
            $companyAddressMapLink = trim($oneLineFromCsv[22]);
            $companyAddressLongitude = trim($oneLineFromCsv[23]);
            $companyAddressLatitude = trim($oneLineFromCsv[24]);
            $companyAddressInstructions = trim($oneLineFromCsv[25]);
            $weatherLocation = trim($oneLineFromCsv[26]);
            $companyTimeZone = trim($oneLineFromCsv[27]);
            $currency = trim($oneLineFromCsv[28]);
            $facebook = trim($oneLineFromCsv[29]);
            $twitter = trim($oneLineFromCsv[30]);
            $instagram = trim($oneLineFromCsv[31]);
            $linkedIn = trim($oneLineFromCsv[32]);
            $homePagePhotosOnly = trim($oneLineFromCsv[33]);
            $includeContactFormHomePage = trim($oneLineFromCsv[34]);
            $includeQRCodeHomePage = trim($oneLineFromCsv[35]);
            $multiLingual = trim($oneLineFromCsv[36]);
            $titleProducts = trim($oneLineFromCsv[37]);
            $titleSubProducts = trim($oneLineFromCsv[38]);
            $titleUsefulLinks = trim($oneLineFromCsv[39]);
            $enableUserRegistration = trim($oneLineFromCsv[40]);
            $websiteContactsEmailAlert = trim($oneLineFromCsv[41]);
            $websiteContactsAutoReply = trim($oneLineFromCsv[42]);
            $registrationEmail = trim($oneLineFromCsv[43]);
            $headerDisplayProducts = trim($oneLineFromCsv[44]);
            $headerDisplaySubProducts = trim($oneLineFromCsv[45]);
            $headerDisplayPhotos = trim($oneLineFromCsv[46]);
            $headerDisplayLogin = trim($oneLineFromCsv[47]);
            $headerDisplayContactDetails = trim($oneLineFromCsv[48]);
            $headerDisplayPricing = trim($oneLineFromCsv[49]);
            $headerDisplayInstructions = trim($oneLineFromCsv[50]);
            $headerDisplayTandCs = trim($oneLineFromCsv[51]);
            $headerDisplayBusinessContacts = trim($oneLineFromCsv[52]);
            $headerDisplayFacebookPages = trim($oneLineFromCsv[53]);
            $facebookReviewsHistoryShowMonths = trim($oneLineFromCsv[54]);
            $headerDisplayCompetitors = trim($oneLineFromCsv[55]);
            $headerDisplayWeather = trim($oneLineFromCsv[56]);
            $footerDisplayContactDetails = trim($oneLineFromCsv[57]);
            $footerDisplayAddress = trim($oneLineFromCsv[58]);
            $footerDisplayTelNumbers = trim($oneLineFromCsv[59]);
            $footerDisplaySocialMedia = trim($oneLineFromCsv[60]);
            $footerDisplayProducts = trim($oneLineFromCsv[61]);
            $footerDisplaySubProducts = trim($oneLineFromCsv[62]);
            $userIncludeHomeAddress = trim($oneLineFromCsv[63]);
            $userIncludeBusinessAddress = trim($oneLineFromCsv[64]);
            $userIncludePersonalDetails = trim($oneLineFromCsv[65]);
            $userIncludeJobDetails = trim($oneLineFromCsv[66]);

            $company_details_id1 = $this->productRepository->findOneBy([
                'id' => '22',
            ]);

            if (!$company_details_id1 and $entity == "CompanyDetails") {
                $company_details = new CompanyDetails();
                $company_details->setCompanyName($companyName)
                    ->setCompanyWebsite($companyWebsite)
                    ->setContactFirstName($contactFirstName)
                    ->setContactLastName($contactLastName)
                    ->setCompanyTel($companyTel)
                    ->setCompanyMobile($companyMobile)
                    ->setCompanySkype($companySkype)
                    ->setFaviconLive($faviconLive)
                    ->setFaviconDev($faviconDev)
                    ->setCompanyQrCode($companyQrCode)
                    ->setCompanyEmail($companyEmail)
                    ->setCompanyEmailPassword($companyEmailPassword)
                    ->setCompanyEmailImportDirectory($companyEmailImportDirectory)
                    ->setCompanyEmailImportProcessedDirectory($companyEmailImportProcessedDirectory)
                    ->setSqlDatabase($sqlDatabase)
                    ->setDatabasePassword($databasePassword)
                    ->setCompanyAddressStreet($companyAddressStreet)
                    ->setCompanyAddressTown($companyAddressTown)
                    ->setCompanyAddressCity($companyAddressCity)
                    ->setCompanyAddressPostalCode($companyAddressPostalCode)
                    ->setCompanyAddressCountry($companyAddressCountry)
                    ->setCompanyAddressMapLink($companyAddressMapLink)
                    ->setCompanyAddressLongitude($companyAddressLongitude)
                    ->setCompanyAddressLatitude($companyAddressLatitude)
                    ->setCompanyAddressInstructions($companyAddressInstructions)
                    ->setWeatherLocation($weatherLocation)
                    ->setCompanyTimeZone($companyTimeZone)
                    ->setCurrency($currency)
                    ->setFacebook($facebook)
                    ->setTwitter($twitter)
                    ->setInstagram($instagram)
                    ->setLinkedIn($linkedIn)
                    ->setHomePagePhotosOnly($homePagePhotosOnly)
                    ->setIncludeContactFormHomePage($includeContactFormHomePage)
                    ->setIncludeQRCodeHomePage($includeQRCodeHomePage)
                    ->setMultiLingual($multiLingual)
                    ->setTitleProducts($titleProducts)
                    ->setTitleSubProducts($titleSubProducts)
                    ->setTitleUsefulLinks($titleUsefulLinks)
                    ->setEnableUserRegistration($enableUserRegistration)
                    ->setWebsiteContactsEmailAlert($websiteContactsEmailAlert)
                    ->setWebsiteContactsAutoReply($websiteContactsAutoReply)
                    ->setRegistrationEmail($registrationEmail)
                    ->setHeaderDisplayProducts($headerDisplayProducts)
                    ->setHeaderDisplaySubProducts($headerDisplaySubProducts)
                    ->setHeaderDisplayPhotos($headerDisplayPhotos)
                    ->setHeaderDisplayLogin($headerDisplayLogin)
                    ->setHeaderDisplayContactDetails($headerDisplayContactDetails)
                    ->setHeaderDisplayPricing($headerDisplayPricing)
                    ->setHeaderDisplayInstructions($headerDisplayInstructions)
                    ->setHeaderDisplayTandCs($headerDisplayTandCs)
                    ->setHeaderDisplayBusinessContacts($headerDisplayBusinessContacts)
                    ->setHeaderDisplayFacebookPages($headerDisplayFacebookPages)
                    ->setFacebookReviewsHistoryShowMonths($facebookReviewsHistoryShowMonths)
                    ->setHeaderDisplayCompetitors($headerDisplayCompetitors)
                    ->setHeaderDisplayWeather($headerDisplayWeather)
                    ->setFooterDisplayContactDetails($footerDisplayContactDetails)
                    ->setFooterDisplayAddress($footerDisplayAddress)
                    ->setFooterDisplayTelNumbers($footerDisplayTelNumbers)
                    ->setFooterDisplaySocialMedia($footerDisplaySocialMedia)
                    ->setFooterDisplayProducts($footerDisplayProducts)
                    ->setFooterDisplaySubProducts($footerDisplaySubProducts)
                    ->setUserIncludeHomeAddress($userIncludeHomeAddress)
                    ->setUserIncludeBusinessAddress($userIncludeBusinessAddress)
                    ->setUserIncludePersonalDetails($userIncludePersonalDetails)
                    ->setUserIncludeJobDetails($userIncludeJobDetails);
                $this->manager->persist($company_details);
                $this->manager->flush();
            }
        }
        $this->manager->flush();
        return null;
    }

    public function __construct(ProductRepository $productRepository, ContainerInterface $container, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->productRepository = $productRepository;
    }
}

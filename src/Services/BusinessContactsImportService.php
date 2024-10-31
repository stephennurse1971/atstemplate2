<?php


namespace App\Services;

use App\Entity\BusinessContacts;
use App\Repository\BusinessContactsRepository;
use App\Repository\BusinessTypesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BusinessContactsImportService
{
    public function importBusinessContacts(string $fileName)
    {
        $status = '';
        $businessOrPerson = '';
        $businessType = '';
        $company = '';
        $firstName = '';
        $lastName = '';
        $website = '';
        $email = '';
        $landline = '';
        $mobile = '';
        $addressStreet = '';
        $addressCity = '';
        $addressCounty = '';
        $addressPostCode = '';
        $addressCountry = '';
        $locationLongitude = '';
        $locationLatitude = '';
        $publicPrivate = '';
        $notes = '';

        $filepath = $this->container->getParameter('business_contacts_import_directory');
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
            $status = trim($oneLineFromCsv[0]);
            $businessOrPerson = trim($oneLineFromCsv[1]);
            $businessType = trim($oneLineFromCsv[2]);
            $company = trim($oneLineFromCsv[3]);
            $firstName = trim($oneLineFromCsv[4]);
            $lastName = trim($oneLineFromCsv[5]);
            $website = trim($oneLineFromCsv[6]);
            $email = trim($oneLineFromCsv[7]);
            $landline = trim($oneLineFromCsv[8]);
            $mobile = trim($oneLineFromCsv[9]);
            $addressStreet = trim($oneLineFromCsv[10]);
            $addressCity = trim($oneLineFromCsv[11]);
            $addressCounty = trim($oneLineFromCsv[12]);
            $addressPostCode = trim($oneLineFromCsv[13]);
            $addressCountry = trim($oneLineFromCsv[14]);
            $locationLongitude = (float)trim($oneLineFromCsv[15]);
            $locationLatitude = (float)trim($oneLineFromCsv[16]);
            $publicPrivate = trim($oneLineFromCsv[17]);
            $notes = trim($oneLineFromCsv[18]);
            if (count($oneLineFromCsv) >= 31) {
                $landline = trim($oneLineFromCsv[9]);
                $landline = str_replace([' ', "(0)", "(", ")", "-", "Switchboard", "+"], "", $landline);
                if ($landline != '') {
                    $landline = "+" . $landline;
                }
                $mobile = trim($oneLineFromCsv[10]);
                $mobile = str_replace([' ', "(0)", "(", ")", "-", "Switchboard", "+"], "", $mobile);
                if ($mobile != '') {
                    $mobile = "+" . $mobile;
                }
            }
            if (count($oneLineFromCsv) >= 91) {
                $website = trim(strtolower($oneLineFromCsv[91]));
            }
            if (count($oneLineFromCsv) < 91) {
                $website = '';
            }
            $businessContact = $this->businessContactsRepository->findOneBy([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'company' => $company,
            ]);

            if (!$businessContact) {
                $businessContact = new BusinessContacts();
                $businessContact->setStatus($status)
                    ->setBusinessOrPerson($businessOrPerson)
                    ->setCompany($company)
                    ->setFirstName($firstName)
                    ->setLastName($lastName)
                    ->setWebsite($website)
                    ->setEmail($email)
                    ->setLandline($landline)
                    ->setMobile($mobile)
                    ->setAddressStreet($addressStreet)
                    ->setAddressCity($addressCity)
                    ->setAddressCounty($addressCounty)
                    ->setAddressPostCode($addressPostCode)
                    ->setAddressCountry($addressCountry)
                    ->setLocationLongitude($locationLongitude)
                    ->setLocationLatitude($locationLatitude)
                    ->setPublicPrivate($publicPrivate)
                    ->setNotes($notes)
                    ->setBusinessType($this->businessTypeRepository->findOneBy([
                        'businessType' => $businessType])
                    );
                $this->manager->persist($businessContact);
                $this->manager->flush();
            }
        }
        $today = new \DateTime('now');
        $this->manager->flush();
        return null;
    }

    public function __construct(BusinessContactsRepository $businessContactsRepository, BusinessTypesRepository $businessTypesRepository, ContainerInterface $container, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->businessContactsRepository = $businessContactsRepository;
        $this->businessTypeRepository = $businessTypesRepository;
    }
}

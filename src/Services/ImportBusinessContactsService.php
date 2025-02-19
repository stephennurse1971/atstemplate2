<?php


namespace App\Services;

use App\Entity\BusinessContacts;
use App\Repository\BusinessContactsRepository;
use App\Repository\BusinessTypesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportBusinessContactsService
{
    public function importBusinessContacts(string $fileName)
    {
        $directories = [
            $this->container->getParameter('business_contacts_import_directory'),
            $this->container->getParameter('project_set_up_import_directory')
        ];
        $fullpath = null;
        foreach ($directories as $directory) {
            $potentialPath = $directory . DIRECTORY_SEPARATOR . $fileName;
            if (file_exists($potentialPath)) {
                $fullpath = $potentialPath;
                break;
            }
        }
        if (!$fullpath) {
            throw new \Exception("File not found in either directory: $fileName");
        }

        $alldataFromCsv = [];
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
            $status = trim($oneLineFromCsv[1]);
            $businessOrPerson = trim($oneLineFromCsv[2]);
            $businessType = trim($oneLineFromCsv[3]);
            $company = trim($oneLineFromCsv[4]);
            $firstName = trim($oneLineFromCsv[5]);
            $lastName = trim($oneLineFromCsv[6]);
            $website = trim($oneLineFromCsv[7]);
            $email = trim($oneLineFromCsv[8]);
            $landline = trim($oneLineFromCsv[9]);
            $mobile = trim($oneLineFromCsv[10]);
            $addressStreet = trim($oneLineFromCsv[11]);
            $addressCity = trim($oneLineFromCsv[12]);
            $addressCounty = trim($oneLineFromCsv[13]);
            $addressPostCode = trim($oneLineFromCsv[14]);
            $addressCountry = trim($oneLineFromCsv[15]);
            $locationLongitude = (float)trim($oneLineFromCsv[16]);
            $locationLatitude = (float)trim($oneLineFromCsv[17]);
            $notes = trim($oneLineFromCsv[18]);

            $landline = str_replace([' ', "(0)", "(", ")", "-", "Switchboard", "+"], "", $landline);
            if ($landline != '') {
                $landline = "+" . $landline;
            }
            $mobile = str_replace([' ', "(0)", "(", ")", "-", "Switchboard", "+"], "", $mobile);
            if ($mobile != '') {
                $mobile = "+" . $mobile;
            }

            $businessContact = $this->businessContactsRepository->findOneBy([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'company' => $company,
            ]);

            if (!$businessContact and $entity=='BusinessContacts') {
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
                    ->setNotes($notes)
                    ->setBusinessType($this->businessTypeRepository->findOneBy([
                        'businessType' => $businessType]));
                $this->manager->persist($businessContact);
                $this->manager->flush();
            }
        }
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

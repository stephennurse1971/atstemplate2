<?php


namespace App\Services;

use App\Entity\BusinessContacts;
use App\Entity\Competitors;
use App\Repository\BusinessContactsRepository;
use App\Repository\BusinessTypesRepository;
use App\Repository\CompetitorsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportCompetitorsService
{
    public function importCompetitors(string $fileName)
    {
        $directories = [
            $this->container->getParameter('competitors_import_directory'),
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
            $companyName = trim($oneLineFromCsv[1]);
            $businessType = trim($oneLineFromCsv[2]);
            $tel = trim($oneLineFromCsv[3]);
            $website = trim($oneLineFromCsv[4]);
            $addressStreet = trim($oneLineFromCsv[5]);
            $addressCity = trim($oneLineFromCsv[6]);
            $addressPostCode = trim($oneLineFromCsv[7]);
            $addressCountry = trim($oneLineFromCsv[8]);
            $locationLongitude = (float)trim($oneLineFromCsv[9]);
            $locationLatitude = (float)trim($oneLineFromCsv[10]);
            $linkedIN = trim($oneLineFromCsv[11]);
            $facebook = trim($oneLineFromCsv[12]);
            $instagram = trim($oneLineFromCsv[13]);
            $twitter = trim($oneLineFromCsv[14]);
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
            $existing_competitor = $this->competitorsRepository->findOneBy([
                'name' => $companyName,
            ]);

            if (!$existing_competitor) {
                $businessContact = new Competitors();
                $businessContact->setName($companyName)
                    ->setType($businessType)
                    ->setTelephone($tel)
                    ->setWebSite($website)
                    ->setCompetitorAddressStreet($addressStreet)
                    ->setCompetitorAddressCity($addressCity)
                    ->setCompetitorAddressPostalCode($addressPostCode)
                    ->setCompetitorAddressCountry($addressCountry)
                    ->setCompetitorAddressLongitude($locationLongitude)
                    ->setCompetitorAddressLatitude($locationLatitude)
                    ->setLinkedIn($linkedIN)
                    ->setFacebook($facebook)
                    ->setInstagram($instagram)
                    ->setTwitter($twitter)
                    ;
                $this->manager->persist($businessContact);
                $this->manager->flush();
            }
        }
        $today = new \DateTime('now');
        $this->manager->flush();
        return null;
    }

    public function __construct(CompetitorsRepository $competitorsRepository, ContainerInterface $container, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->competitorsRepository = $competitorsRepository;
    }
}

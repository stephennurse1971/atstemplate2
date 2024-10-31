<?php


namespace App\Services;

use App\Entity\BusinessContacts;
use App\Entity\BusinessTypes;
use App\Repository\BusinessContactsRepository;
use App\Repository\BusinessTypesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BusinessTypesImportService
{
    public function importBusinessTypes(string $fileName)
    {
        $ranking = '';
        $businessType = '';
        $mapIcon = '';
        $mapIconColour = '';
        $mapDisplay = '';

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
            $ranking = trim($oneLineFromCsv[0]);
            $businessType = trim($oneLineFromCsv[1]);
            $mapIcon = trim($oneLineFromCsv[2]);
            $mapIconColour = trim($oneLineFromCsv[3]);
            $mapDisplay = trim($oneLineFromCsv[4]);

            if (!$businessType) {
                $businessType = new BusinessTypes();
                $businessType->setRanking($ranking)
                    ->setBusinessType($businessType)
                    ->setMapIcon($mapIcon)
                    ->setMapIconColour($mapIconColour)
                    ->setMapDisplay($mapDisplay);
                $this->manager->persist($businessType);
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

<?php


namespace App\Services;

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
        $businessDescription = '';
        $mapIcon = '';
        $mapIcon2 = '';
        $mapIconColour = '';
        $mapDisplay = '';
        $filepath = $this->container->getParameter('business_types_import_directory');
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
            $businessTypeName = trim($oneLineFromCsv[1]);
            $businessDescription = trim($oneLineFromCsv[2]);
            $mapIcon = trim($oneLineFromCsv[3]);
            $mapIcon2 = trim($oneLineFromCsv[4]);
            $mapIconColour = trim($oneLineFromCsv[5]);
            $mapDisplay = trim($oneLineFromCsv[6]);

            $previous_business_type = $this->businessTypeRepository->findOneBy(['businessType' => $businessTypeName]);

            if (!$previous_business_type) {
                $businessType = new BusinessTypes();
                $businessType->setRanking($ranking)
                    ->setBusinessType($businessTypeName)
                    ->setDescription($businessDescription)
                    ->setMapIcon($mapIcon)
                    ->setMapIcon2($mapIcon2)
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

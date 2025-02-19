<?php


namespace App\Services;

use App\Entity\MapIcons;
use App\Repository\MapIconsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportMapIconsService
{
    public function importMapIcons(string $fileName)
    {
        $directories = [
            $this->container->getParameter('map_icons_import_directory'),
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
            $name = trim($oneLineFromCsv[1]);
            $fileName = trim($oneLineFromCsv[2]);

            $mapIcon = $this->mapIconsRepository->findOneBy([
                'name' => $name,
                'iconFile' => $fileName,
            ]);

            if (!$mapIcon and $entity =='MapIcons') {
                $mapIcon = new MapIcons();
                $mapIcon->setName($name)
                    ->setIconFile($fileName);
                $this->manager->persist($mapIcon);
                $this->manager->flush();
            }
        }
        $this->manager->flush();
        return null;
    }

    public function __construct(MapIconsRepository $mapIconsRepository, ContainerInterface $container, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->mapIconsRepository = $mapIconsRepository;
    }
}

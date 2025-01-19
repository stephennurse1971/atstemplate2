<?php


namespace App\Services;

use App\Entity\MapIcons;
use App\Repository\MapIconsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MapIconsImportService
{
    public function importMapIcons(string $fileName)
    {
        $name = '';
        $link = '';
        $comment = '';
        $filepath = $this->container->getParameter('map_icons_import_directory');
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
            $name = trim($oneLineFromCsv[0]);
            $fileName = trim($oneLineFromCsv[1]);

            $mapIcon = $this->mapIconsRepository->findOneBy([
                'name' => $name,
                'iconFile' => $fileName,
            ]);

            if (!$mapIcon) {
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

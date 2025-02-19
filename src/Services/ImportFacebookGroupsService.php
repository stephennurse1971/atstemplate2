<?php


namespace App\Services;

use App\Entity\FacebookGroups;
use App\Repository\FacebookGroupsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportFacebookGroupsService
{
    public function importFacebookGroups(string $fileName)
    {
        $directories = [
            $this->container->getParameter('facebook_groups_import_directory'),
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
            $link = trim($oneLineFromCsv[2]);
            $comments = trim($oneLineFromCsv[3]);

            $facebookGroup = $this->facebookGroupsRepository->findOneBy([
                'name' => $name,
                'link' => $link,
            ]);

            if (!$facebookGroup and $entity=="FacebookGroups") {
                $facebookGroup = new FacebookGroups();
                $facebookGroup->setName($name)
                    ->setLink($link)
                    ->setComments($comments);
                $this->manager->persist($facebookGroup);
                $this->manager->flush();
            }
        }
        $this->manager->flush();
        return null;
    }

    public function __construct(FacebookGroupsRepository $facebookGroupsRepository, ContainerInterface $container, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->facebookGroupsRepository = $facebookGroupsRepository;
    }
}

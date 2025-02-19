<?php

namespace App\Services;

use App\Entity\Languages;
use App\Repository\LanguagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportLanguagesService
{
    private LanguagesRepository $languagesRepository;

    public function importLanguages(string $fileName)
    {
        $directories = [
            $this->container->getParameter('languages_import_directory'),
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
            $ranking = trim($oneLineFromCsv[1]);
            $isActive = trim($oneLineFromCsv[2]);
            $languageName = trim($oneLineFromCsv[3]);
            $abbreviation = trim($oneLineFromCsv[4]);
            $linkedInOther = trim($oneLineFromCsv[5]);
            $icon = trim($oneLineFromCsv[6]);

            $previous_language = $this->languagesRepository->findOneBy(['language' => $languageName]);
            if (!$previous_language and $entity=='Languages') {
                $language = new Languages();
                $language->setRanking($ranking)
                    ->setIsActive($isActive)
                    ->setLanguage($languageName)
                    ->setAbbreviation($abbreviation)
                    ->setLinkedInOther($linkedInOther)
                    ->setIcon($icon);
                $this->manager->persist($language);
                $this->manager->flush();
            }
        }
        $this->manager->flush();
        return null;
    }

    public function __construct(LanguagesRepository $languagesRepository, ContainerInterface $container, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->languagesRepository = $languagesRepository;
    }
}

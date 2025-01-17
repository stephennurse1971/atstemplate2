<?php

namespace App\Services;

use App\Entity\Languages;
use App\Form\LanguagesType;
use App\Repository\LanguagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LanguagesImportService
{
    private LanguagesRepository $languagesRepository;

    public function importLanguages(string $fileName)
    {
        $ranking = '';
        $isActive = '';
        $language = '';
        $abbreviation = '';
        $linkedInOther = '';
        $icon = '';
        $filepath = $this->container->getParameter('languages_import_directory');
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
            $isActive = trim($oneLineFromCsv[1]);
            $languageName = trim($oneLineFromCsv[2]);
            $abbreviation = trim($oneLineFromCsv[3]);
            $linkedInOther = trim($oneLineFromCsv[4]);
            $icon = trim($oneLineFromCsv[5]);

            $previous_language = $this->languagesRepository->findOneBy(['language' => $language]);
            if (!$previous_language) {
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

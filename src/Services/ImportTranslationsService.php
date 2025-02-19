<?php

namespace App\Services;

use App\Entity\Translation;
use App\Repository\TranslationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportTranslationsService
{
    public function importTranslations(string $fileName)
    {
        $directories = [
            $this->container->getParameter('translations_import_directory'),
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
                    $alldatatsFromCsv[$row][] = $data[$c];
                }
            }
            fclose($handle);
        }

        foreach ($alldatatsFromCsv as $oneLineFromCsv) {
            $entity = trim($oneLineFromCsv[0]);
            $where_used = trim($oneLineFromCsv[1]);
            $english = trim($oneLineFromCsv[2]);
            $french = trim($oneLineFromCsv[3]);
            $german = trim($oneLineFromCsv[4]);
            $spanish = trim($oneLineFromCsv[5]);
            $russian = trim($oneLineFromCsv[6]);
            $old_translation = $this->translationRepository->findOneBy(['english' => $english]);
            if ($old_translation) {
                if ($old_translation->getEntity() == $entity &&
                    $old_translation->getEnglish() == $english &&
                    $old_translation->getFrench() == $french &&
                    $old_translation->getGerman() == $german &&
                    $old_translation->getSpanish() == $spanish &&
                    $old_translation->getRussian() == $russian) {
                    continue;
                } else {
                    $old_translation
                        ->setEntity($entity)
                        ->setFrench($french)
                        ->setGerman($german)
                        ->setSpanish($spanish)
                        ->setRussian($russian);
                    $this->manager->persist($old_translation);
                    $this->manager->flush();
                }
            }
            if (!$old_translation and $entity == 'Translations' ) {
                $new_translation = new Translation();
                $new_translation->setEnglish($where_used)
                    ->setEnglish($english)
                    ->setFrench($french)
                    ->setGerman($german)
                    ->setSpanish($spanish)
                    ->setRussian($russian);
                $this->manager->persist($new_translation);
                $this->manager->flush();
            }
        }
        return null;
    }

    public
    function __construct(ContainerInterface $container, TranslationRepository $translationRepository, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->translationRepository = $translationRepository;
    }
}

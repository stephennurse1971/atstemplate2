<?php


namespace App\Services;


use App\Entity\Import;
use App\Entity\Translation;
use App\Repository\TranslationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TranslationsImportService
{
    public function importTranslations(string $fileName)
    {
        $filepath = $this->container->getParameter('translations_directory');
        $fullpath = $filepath . $fileName;
        $alldatatsFromCsv = [];
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
            $english = trim($oneLineFromCsv[0]);
            $french = trim($oneLineFromCsv[1]);
            $german = trim($oneLineFromCsv[2]);
            $spanish = trim($oneLineFromCsv[3]);
            $russian = trim($oneLineFromCsv[4]);
            $old_translation = $this->translationRepository->findOneBy(['english' => $english ]);
            if ($old_translation) {
                if ($old_translation->getEnglish() == $english &&
                    $old_translation->getFrench() == $french &&
                    $old_translation->getGerman() == $german &&
                    $old_translation->getSpanish() == $spanish &&
                    $old_translation->getRussian() == $russian
                ) {
                   continue;
                } else {
                    $old_translation
                        ->setFrench($french)
                        ->setGerman($german)
                        ->setSpanish($spanish)
                        ->setRussian($russian);

                    $this->manager->flush();
                }
            }
            else{
                $new_translation = new Translation();
                $new_translation->setEnglish($english)
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

    public function __construct(ContainerInterface $container, TranslationRepository $translationRepository, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->translationRepository = $translationRepository;
    }
}

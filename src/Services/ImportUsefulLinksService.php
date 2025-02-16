<?php


namespace App\Services;

use App\Entity\Product;
use App\Entity\UsefulLinks;
use App\Repository\UsefulLinksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportUsefulLinksService
{
    public function importUsefulLink(string $fileName)
    {
        $name = '';
        $link = '';
        $comment = '';

        $filepath = $this->container->getParameter('useful_links_import_directory');
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
            $entity =  trim($oneLineFromCsv[0]);
            $category = trim($oneLineFromCsv[1]);
            $name = trim($oneLineFromCsv[2]);
            $link = trim($oneLineFromCsv[3]);
            $notes = trim($oneLineFromCsv[4]);
            $login = trim($oneLineFromCsv[5]);
            $password = trim($oneLineFromCsv[6]);
            $private = trim($oneLineFromCsv[7]);

            $usefulLinkExists = $this->usefulLinksRepository->findOneBy([
                'link' => "$link",
                'category' => $category,
            ]);

            if (!$usefulLinkExists and $entity=='UsefulLinks') {
                $product = new UsefulLinks();
                $product->setCategory($category)
                    ->setName($name)
                    ->setLink($link)
                    ->setNotes($notes)
                    ->setLogin($login)
                    ->setPassword($password)
                    ->setPrivate($private);
                $this->manager->persist($product);
                $this->manager->flush();
            }
        }
        $this->manager->flush();
        return null;
    }

    public function __construct(UsefulLinksRepository $usefulLinksRepository, ContainerInterface $container, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->usefulLinksRepository = $usefulLinksRepository;
    }
}

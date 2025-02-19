<?php


namespace App\Services;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportProductsService
{
    public function importProducts(string $fileName)
    {
        $directories = [
            $this->container->getParameter('products_import_directory'),
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
            $entity =  trim($oneLineFromCsv[0]);
            $ranking = trim($oneLineFromCsv[1]);
            $category = trim($oneLineFromCsv[2]);
            $productName = trim($oneLineFromCsv[3]);
            $isActive = trim($oneLineFromCsv[4]);
            $includeInFooter = trim($oneLineFromCsv[5]);
            $newClientEmail = trim($oneLineFromCsv[6]);
            $notes = trim($oneLineFromCsv[7]);


            $product = $this->productRepository->findOneBy([
                'category' => $category,
                'product' => $productName,
            ]);

            if (!$product and $entity=='Products') {
                $product = new Product();
                $product->setRanking((int)$ranking)
                    ->setCategory($category)
                    ->setProduct($productName)
                    ->setIsActive($isActive)
                    ->setIncludeInFooter($includeInFooter)
                    ->setNewClientEmail($newClientEmail)
                    ->setNotes($notes);
                $this->manager->persist($product);
                $this->manager->flush();
            }
        }
        $this->manager->flush();
        return null;
    }

    public function __construct(ProductRepository $productRepository, ContainerInterface $container, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->productRepository = $productRepository;
    }
}

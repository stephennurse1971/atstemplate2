<?php


namespace App\Services;

use App\Entity\CompanyDetails;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportCompanyDetailsService
{
    public function importCompanyDetails(string $fileName)
    {
        $name = '';
        $link = '';
        $comment = '';

        $filepath = $this->container->getParameter('company_details_import_directory');
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
            $email = trim($oneLineFromCsv[1]);
            $sqldatabase = trim($oneLineFromCsv[2]);
            $sqlpassword = trim($oneLineFromCsv[3]);

            $company_details = $this->productRepository->findOneBy([
                'id' => '1',
            ]);

            if (!$company_details) {
                $company_details = new CompanyDetails();
                $company_details->setCompanyName($name)
                    ->setCompanyEmail($email)
                    ->setSqlDatabase($sqldatabase)
                    ->setDatabasePassword($sqlpassword)
                ;
                $this->manager->persist($company_details);
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

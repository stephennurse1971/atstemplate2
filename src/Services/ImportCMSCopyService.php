<?php


namespace App\Services;

use App\Entity\CmsCopy;
use App\Repository\CmsCopyPageFormatsRepository;
use App\Repository\CmsCopyRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportCMSCopyService
{
    public function importCMSCopy(string $fileName)
    {
        $directories = [
            $this->container->getParameter('cms_copy_import_directory'),
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
            $category = trim($oneLineFromCsv[1]);
            $productName = trim($oneLineFromCsv[2]);

            $ranking = trim($oneLineFromCsv[3]);
            $hyperlink = trim($oneLineFromCsv[4]);
            $attachment = trim($oneLineFromCsv[5]);
            $pageCountUser = trim($oneLineFromCsv[6]);
            $pageCountAdmin = trim($oneLineFromCsv[7]);
            $pageLayoutName = trim($oneLineFromCsv[8]);

            $tabTitle = trim($oneLineFromCsv[9]);
            $tabTitleFR = trim($oneLineFromCsv[10]);
            $tabTitleDE = trim($oneLineFromCsv[11]);

            $contentTitle = trim($oneLineFromCsv[12]);
            $contentTitleFR = trim($oneLineFromCsv[13]);
            $contentTitleDE = trim($oneLineFromCsv[14]);

            $contentText = trim($oneLineFromCsv[15]);
            $contentTextFR = trim($oneLineFromCsv[16]);
            $contentTextDE = trim($oneLineFromCsv[17]);

            $product = $this->productRepository->findOneBy(['product' => $productName]);
            $pageLayout = $this->cmsCopyPageFormatsRepository->findOneBy(['name' => $pageLayoutName]);

            $cms_copy = $this->cmsCopyRepository->findOneBy([
                'category' => $category,
                'ranking' => $ranking,
                'tabTitle'=>$tabTitle
            ]);

            if (!$cms_copy and $entity == 'CMSCopy') {
                $cms_copy = new CmsCopy();
                $cms_copy->setCategory($category)
                    ->setProduct($product)
                    ->setRanking((int)$ranking)
                    ->setHyperlinks($hyperlink)
                    ->setAttachment($attachment)
                    ->setPageCountUsers((int)$pageCountUser)
                    ->setPageCountAdmin((int)$pageCountAdmin)
                    ->setPageLayout($pageLayout)
                    ->setTabTitle($tabTitle)
                    ->setTabTitleFR($tabTitleFR)
                    ->setTabTitleDE($tabTitleDE)
                    ->setContentTitle($contentTitle)
                    ->setContentTitleFR($contentTitleFR)
                    ->setContentTitleDE($contentTitleDE)
                    ->setContentText($contentText)
                    ->setContentTextFR($contentTextFR)
                    ->setContentTextDE($contentTextDE);
                $this->manager->persist($cms_copy);
                $this->manager->flush();
            }
        }
        $this->manager->flush();
        return null;
    }

    public function __construct(CmsCopyRepository $cmsCopyRepository, ProductRepository $productRepository, CmsCopyPageFormatsRepository $cmsCopyPageFormatsRepository, ContainerInterface $container, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->cmsCopyRepository = $cmsCopyRepository;
        $this->productRepository = $productRepository;
        $this->cmsCopyPageFormatsRepository = $cmsCopyPageFormatsRepository;
    }
}

<?php


namespace App\Services;

use App\Entity\CmsPhoto;
use App\Repository\CmsCopyPageFormatsRepository;
use App\Repository\CmsCopyRepository;
use App\Repository\CmsPhotoRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ImportCMSPhotoService
{
    public function importCMSPhoto(string $fileName)
    {
        $directories = [
            $this->container->getParameter('cms_photos_import_directory'),
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
            $staticPageName = trim($oneLineFromCsv[3]);
            $ranking = trim($oneLineFromCsv[4]);
            $title = trim($oneLineFromCsv[5]);
            $hyperlink = trim($oneLineFromCsv[6]);
            $photoOrVideo = trim($oneLineFromCsv[7]);
            $rotate = trim($oneLineFromCsv[8]);
            $photoFile = trim($oneLineFromCsv[9]);

            $product = $this->productRepository->findOneBy(['product' => $productName]);

            $cms_photo = $this->cmsPhotoRepository->findOneBy([
                'photo' => $photoFile,
            ]);

            if (!$cms_photo and $entity == 'CMSPhoto') {
                $cms_photo = new CmsPhoto();
                $cms_photo->setCategory($category)
                    ->setProduct($product)
                    ->setStaticPageName($staticPageName)
                    ->setRanking($ranking)
                    ->setTitle($title)
                    ->setLink($hyperlink)
                    ->setPhotoOrVideo($photoOrVideo)
                    ->setRotate($rotate)
                    ->setPhoto($photoFile);
                $this->manager->persist($cms_photo);
                $this->manager->flush();
            }
        }
        $this->manager->flush();
        return null;
    }

    public function __construct(CmsPhotoRepository $cmsPhotoRepository, ProductRepository $productRepository, ContainerInterface $container, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->cmsPhotoRepository = $cmsPhotoRepository;
        $this->productRepository = $productRepository;

    }
}

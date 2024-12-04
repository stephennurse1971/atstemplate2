<?php


namespace App\Services;

use App\Entity\FacebookGroupsReviews;
use App\Repository\FacebookGroupsReviewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FacebookGroupsReviewsImportService
{
    public function importFacebookGroupsReviews(string $fileName)
    {
        $name = '';
        $reviewer = '';
        $date = '';
        $comments = '';

        $filepath = $this->container->getParameter('facebook_groups_reviews_import_directory');
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
            $reviewer = trim($oneLineFromCsv[1]);
            $date = trim($oneLineFromCsv[2]);
            $comments = trim($oneLineFromCsv[3]);

            $facebookGroupReview = $this->facebookGroupsReviewsRepository->findOneBy([
                'name' => $name,
                'reviewer' => $reviewer,
                'date' => $date,
            ]);

            if (!$facebookGroupReview) {
                $facebookGroupReview = new FacebookGroupsReviews();
                $facebookGroupReview->setFacebookGroup('XXX')
                    ->setReviewer($reviewer)
                    ->setDate($date)
                    ->setComment($comments);
                $this->manager->persist($facebookGroupReview);
                $this->manager->flush();
            }
        }
        $this->manager->flush();
        return null;
    }

    public function __construct(FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository, ContainerInterface $container, EntityManagerInterface $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
        $this->facebookGroupsReviewsRepository = $facebookGroupsReviewsRepository;
    }
}

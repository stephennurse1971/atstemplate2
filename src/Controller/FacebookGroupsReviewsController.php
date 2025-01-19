<?php

namespace App\Controller;

use App\Entity\FacebookGroupsReviews;
use App\Form\FacebookGroupsReviewsType;
use App\Form\ImportType;
use App\Repository\FacebookGroupsRepository;
use App\Repository\FacebookGroupsReviewsRepository;
use App\Services\FacebookGroupsReviewsImportService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/facebook_group_reviews")
 */
class FacebookGroupsReviewsController extends AbstractController
{
    /**
     * @Route("/index", name="facebook_groups_reviews_index", methods={"GET"})
     */
    public function index(FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository): Response
    {
        return $this->render('facebook_groups_reviews/index.html.twig', [
            'facebook_groups_reviews' => $facebookGroupsReviewsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{facebookGroupId}", name="facebook_groups_reviews_new", methods={"GET", "POST"}, defaults={"facebookGroupId"="NULL"})
     */
    public function new(Request $request, $facebookGroupId, FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository, FacebookGroupsRepository $facebookGroupsRepository): Response
    {
        $facebookGroupName = '';
        $facebookGroupsReview = new FacebookGroupsReviews();
        $facebookGroup = $facebookGroupsRepository->findAll();
        $form = $this->createForm(FacebookGroupsReviewsType::class, $facebookGroupsReview);
        if ($facebookGroupId > 0) {
            $facebookGroup = $facebookGroupsRepository->findBy(['id' => $facebookGroupId]);
            $facebookGroupName = $facebookGroupsRepository->find($facebookGroupId)->getName();
        }
        $form = $this->createForm(FacebookGroupsReviewsType::class, $facebookGroupsReview, ['facebookGroup' => $facebookGroup, 'mode' => 'new']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facebookGroupsReviewsRepository->add($facebookGroupsReview, true);
            return $this->redirectToRoute('facebook_groups_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facebook_groups_reviews/new.html.twig', [
            'facebook_groups_review' => $facebookGroupsReview,
            'form' => $form,
            'facebookGroupName' => $facebookGroupName,
        ]);
    }


    /**
     * @Route("/new_nothing_of_note/{facebookGroupId}", name="facebook_groups_reviews_new_nothing_of_note", methods={"GET", "POST"}, defaults={"facebookGroupId"="NULL"})
     */
    public function newNothingOfNote(Request $request, $facebookGroupId, FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository, FacebookGroupsRepository $facebookGroupsRepository, Security $security): Response
    {
        $user = $security->getUser();
        $facebookGroupsReview = new FacebookGroupsReviews();
        $now = new \DateTime('now');
        if ($facebookGroupId > 0) {
            $facebookGroupsReview->setFacebookGroup($facebookGroupsRepository->find($facebookGroupId));
            $facebookGroupsReview->setDate($now);
            $facebookGroupsReview->setComment('Nothing of note');
            $facebookGroupsReview->setReviewer($user);
        }
        $facebookGroupsReviewsRepository->add($facebookGroupsReview, true);
        return $this->redirectToRoute('facebook_groups_index', [], Response::HTTP_SEE_OTHER);

    }

    /**
     * @Route("/show/{id}", name="facebook_groups_reviews_show", methods={"GET"})
     */
    public function show(FacebookGroupsReviews $facebookGroupsReview): Response
    {
        return $this->render('facebook_groups_reviews/show.html.twig', [
            'facebook_groups_review' => $facebookGroupsReview,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="facebook_groups_reviews_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, FacebookGroupsReviews $facebookGroupsReview, FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository): Response
    {
        $form = $this->createForm(FacebookGroupsReviewsType::class, $facebookGroupsReview);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facebookGroupsReviewsRepository->add($facebookGroupsReview, true);
            return $this->redirectToRoute('facebook_groups_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facebook_groups_reviews/edit.html.twig', [
            'facebook_groups_review' => $facebookGroupsReview,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="facebook_groups_reviews_delete", methods={"POST"})
     */
    public function delete(Request $request, FacebookGroupsReviews $facebookGroupsReview, FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $facebookGroupsReview->getId(), $request->request->get('_token'))) {
            $facebookGroupsReviewsRepository->remove($facebookGroupsReview, true);
        }

        return $this->redirectToRoute('facebook_groups_reviews_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete_all", name="facebook_groups_reviews_delete_all")
     */
    public function deleteAllFacebookGroupsReviews(FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository, EntityManagerInterface $entityManager): Response
    {
        $facebook_groups_reviews = $facebookGroupsReviewsRepository->findAll();
        foreach ($facebook_groups_reviews as $facebook_groups_review) {
            $entityManager->remove($facebook_groups_review);
            $entityManager->flush();
        }
        return $this->redirectToRoute('facebook_groups_reviews_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route ("/export", name="facebook_groups_reviews_export" )
     */
    public function facebookGroupsReviewsExport(FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository)
    {
        $data = [];
        $exported_date = new \DateTime('now');
        $exported_date_formatted = $exported_date->format('d-M-Y');
        $exported_date_formatted_for_file = $exported_date->format('d-m-Y');
        $fileName = 'facebook_groups_reviews_export_' . $exported_date_formatted_for_file . '.csv';

        $count = 0;
        $facebook_groups_reviews_list = $facebookGroupsReviewsRepository->findAll();
        foreach ($facebook_groups_reviews_list as $facebook_groups_review) {
            $concatenatedNotes = "Exported on: " . $exported_date_formatted;
            $data[] = [
                $facebook_groups_review->getFacebookGroup()->getName(),
//                $facebook_groups_review->getReviewer()->getFullName(),
                'XXX',
                $facebook_groups_review->getDate()->format('d-M-Y'),
                $facebook_groups_review->getComment(),
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Facebook Groups Reviews');
        $sheet->getCell('A1')->setValue('Facebook Group');
        $sheet->getCell('B1')->setValue('Reviewer');
        $sheet->getCell('C1')->setValue('Date');
        $sheet->getCell('D1')->setValue('Comments');
        $sheet->getCell('E1')->setValue($concatenatedNotes);

        $sheet->fromArray($data, null, 'A2', true);
        $total_rows = $sheet->getHighestRow();
        for ($i = 2; $i <= $total_rows; $i++) {
            $cell = "L" . $i;
            $sheet->getCell($cell)->getHyperlink()->setUrl("https://google.com");
        }
        $writer = new Csv($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', sprintf('attachment;filename="%s"', $fileName));
        $response->headers->set('Cache-Control', 'max-age=0');
        return $response;
    }


    /**
     * @Route ("/import", name="facebook_groups_reviews_import" )
     */
    public function facebookGroupsReviewsImport(Request $request, SluggerInterface $slugger,  FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository, FacebookGroupsReviewsImportService $facebookGroupsReviewsImportService): Response
    {
        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $importFile = $form->get('File')->getData();
            if ($importFile) {
                $originalFilename = pathinfo($importFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '.' . 'csv';
                try {
                    $importFile->move(
                        $this->getParameter('facebook_groups_reviews_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $facebookGroupsReviewsImportService->importFacebookGroupsReviews($newFilename);
                return $this->redirectToRoute('facebook_groups_reviews_index');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'Facebook Groups Reviews Import',
        ]);
    }


}

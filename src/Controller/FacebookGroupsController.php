<?php

namespace App\Controller;

use App\Entity\FacebookGroups;
use App\Form\FacebookGroupsType;
use App\Form\ImportType;
use App\Repository\CompanyDetailsRepository;
use App\Repository\FacebookGroupsRepository;
use App\Repository\FacebookGroupsReviewsRepository;
use App\Services\FacebookGroupsImportService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/facebook_groups")
 */
class FacebookGroupsController extends AbstractController
{
    /**
     * @Route("/index", name="facebook_groups_index", methods={"GET"})
     */
    public function index(FacebookGroupsRepository $facebookGroupsRepository, FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository, CompanyDetailsRepository $companyDetailsRepository): Response
    {
        $company_details = $companyDetailsRepository->find('1');
        $today = new \DateTime('now');
        $history_months = max(1,$company_details->getFacebookReviewsHistoryShowMonths());
        $cut_off_date = (clone $today)->modify("-{$history_months} months");

        return $this->render('facebook_groups/index.html.twig', [
            'facebook_groups' => $facebookGroupsRepository->findAll(),
            'facebook_group_reviews' => $facebookGroupsReviewsRepository->findByDateLatest(),
            'cut_off_date' => $cut_off_date,
        ]);
    }

    /**
     * @Route("/new", name="facebook_groups_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FacebookGroupsRepository $facebookGroupsRepository): Response
    {
        $facebookGroup = new FacebookGroups();
        $form = $this->createForm(FacebookGroupsType::class, $facebookGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facebookGroupsRepository->add($facebookGroup, true);

            return $this->redirectToRoute('facebook_groups_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facebook_groups/new.html.twig', [
            'facebook_group' => $facebookGroup,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/show/{id}", name="facebook_groups_show", methods={"GET"})
     */
    public function show(FacebookGroups $facebookGroup): Response
    {
        return $this->render('facebook_groups/show.html.twig', [
            'facebook_group' => $facebookGroup,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="facebook_groups_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, FacebookGroups $facebookGroup, FacebookGroupsRepository $facebookGroupsRepository): Response
    {
        $form = $this->createForm(FacebookGroupsType::class, $facebookGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facebookGroupsRepository->add($facebookGroup, true);

            return $this->redirectToRoute('facebook_groups_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facebook_groups/edit.html.twig', [
            'facebook_group' => $facebookGroup,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="facebook_groups_delete", methods={"POST"})
     */
    public function delete(Request $request, FacebookGroups $facebookGroup, FacebookGroupsRepository $facebookGroupsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facebookGroup->getId(), $request->request->get('_token'))) {
            $facebookGroupsRepository->remove($facebookGroup, true);
        }

        return $this->redirectToRoute('facebook_groups_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/delete_all", name="facebook_groups_delete_all")
     */
    public function deleteAllFacebookGroups(FacebookGroupsRepository $facebookGroupsRepository, EntityManagerInterface $entityManager): Response
    {
        $facebook_groups = $facebookGroupsRepository->findAll();
        foreach ($facebook_groups as $facebook_group) {
            $entityManager->remove($facebook_group);
            $entityManager->flush();
        }
        return $this->redirectToRoute('facebook_groups_index', [], Response::HTTP_SEE_OTHER);
    }



    /**
     * @Route("/change_facebook_history_months/{change}", name="change_facebook_history_months", methods={"GET", "POST"})
     */
    public
    function changePriority(Request $request, $change, CompanyDetailsRepository $companyDetailsRepository, EntityManagerInterface $manager): Response
    {
        $referer = $request->headers->get('Referer');
        $company_details=$companyDetailsRepository->findOneBy(["id"=>'1']);
        $currentFacebookHistoryMonths = $company_details->getFacebookReviewsHistoryShowMonths();

        if ($change == "Up") {
            $newMonthsCount = $currentFacebookHistoryMonths + 1;
        }
        if ($change == "Down") {
            $newMonthsCount = $currentFacebookHistoryMonths - 1;
        }
        $company_details->setFacebookReviewsHistoryShowMonths($newMonthsCount);
        $manager->flush();
        return $this->redirect($referer);
    }

    /**
     * @Route ("/export", name="facebook_groups_export" )
     */
    public function facebookGroupsExport(FacebookGroupsRepository $facebookGroupsRepository)
    {
        $data = [];
        $exported_date = new \DateTime('now');
        $exported_date_formatted = $exported_date->format('d-M-Y');
        $fileName = 'facebook_groups_export_' . $exported_date_formatted . '.csv';

        $count = 0;
        $facebook_groups_list = $facebookGroupsRepository->findAll();
        $concatenatedNotes = "Exported on: " . $exported_date_formatted;
        foreach ($facebook_groups_list as $facebook_groups) {
            $data[] = [
                $facebook_groups->getName(),
                $facebook_groups->getLink(),
                $facebook_groups->getComments(),
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Facebook Groups');
        $sheet->getCell('A1')->setValue('Name');
        $sheet->getCell('B1')->setValue('Link');
        $sheet->getCell('C1')->setValue('Comments');
        $sheet->getCell('D1')->setValue($concatenatedNotes);

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
     * @Route ("/import", name="facebook_groups_import" )
     */
    public function facebookGroupsImport(Request $request, SluggerInterface $slugger, FacebookGroupsRepository $facebookGroupsRepository, FacebookGroupsImportService $facebookGroupsImportService): Response
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
                        $this->getParameter('facebook_groups_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $facebookGroupsImportService->importFaceBookGroups($newFilename);
                return $this->redirectToRoute('facebook_groups_index');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'Facebook Groups Import',
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\BusinessTypes;
use App\Form\BusinessTypesType;
use App\Form\ImportType;
use App\Repository\BusinessTypesRepository;
use App\Services\BusinessTypesImportService;
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
 * @Route("/business/types")
 */
class BusinessTypesController extends AbstractController
{
    /**
     * @Route("/index", name="business_types_index", methods={"GET"})
     */
    public function index(BusinessTypesRepository $businessTypesRepository): Response
    {
        return $this->render('business_types/index.html.twig', [
            'business_types' => $businessTypesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="business_types_new", methods={"GET", "POST"})
     */
    public function new(Request $request, BusinessTypesRepository $businessTypesRepository): Response
    {
        $businessType = new BusinessTypes();
        $form = $this->createForm(BusinessTypesType::class, $businessType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $businessTypesRepository->add($businessType, true);

            return $this->redirectToRoute('business_types_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('business_types/new.html.twig', [
            'business_type' => $businessType,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/show/{id}", name="business_types_show", methods={"GET"})
     */
    public function show(BusinessTypes $businessType): Response
    {
        return $this->render('business_types/show.html.twig', [
            'business_type' => $businessType,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="business_types_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, BusinessTypes $businessType, BusinessTypesRepository $businessTypesRepository): Response
    {
        $form = $this->createForm(BusinessTypesType::class, $businessType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $businessTypesRepository->add($businessType, true);

            return $this->redirectToRoute('business_types_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('business_types/edit.html.twig', [
            'business_type' => $businessType,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="business_types_delete", methods={"POST"})
     */
    public function delete(Request $request, BusinessTypes $businessType, BusinessTypesRepository $businessTypesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $businessType->getId(), $request->request->get('_token'))) {
            $businessTypesRepository->remove($businessType, true);
        }

        return $this->redirectToRoute('business_types_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete_all", name="business_types_delete_all")
     */
    public function deleteAllBusinessTypes(BusinessTypesRepository $businessTypesRepository, EntityManagerInterface $entityManager): Response
    {
        $business_contacts = $businessTypesRepository->findAll();
        foreach ($business_contacts as $business_contact) {
            $entityManager->remove($business_contact);
            $entityManager->flush();
        }
        return $this->redirectToRoute('business_types_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/change_ranking/{change}/{id}", name="business_type_change_ranking", methods={"GET", "POST"})
     */
    public
    function changePriority(Request $request, $change, BusinessTypes $businessTypes, EntityManagerInterface $manager): Response
    {
        $referer = $request->headers->get('Referer');
        $currentRanking = $businessTypes->getRanking();
        if ($change == "Up") {
            $newRanking = $currentRanking - 1.01;
        }
        if ($change == "Down") {
            $newRanking = $currentRanking + 1.01;
        }
        $businessTypes->setRanking($newRanking);
        $manager->flush();
        return $this->redirect($referer);
    }

    /**
     * @Route("/update_business_type_ranking", name="update_business_type_ranking", methods={"GET", "POST"})
     */
    public function updateRankingWithInteger(BusinessTypesRepository $businessTypesRepository, Request $request, EntityManagerInterface $manager): Response
    {
        $referer = $request->headers->get('Referer');
        $business_types = $businessTypesRepository->findAll();
        $rankingContainer = [];
        foreach ($business_types as $business_type) {
            $rankingContainer[] = ['id' => $business_type->getId(), 'ranking' => $business_type->getRanking()];
            array_multisort(array_column($rankingContainer, 'ranking'), SORT_ASC, $rankingContainer);
            $minRank = 1;
            foreach ($rankingContainer as $sortedItem) {
                $sortedItem = $businessTypesRepository->find($sortedItem['id']);
                $sortedItem->setRanking($minRank);
                $manager->flush();
                $minRank = $minRank + 1;
            }
        }

        return $this->redirect($referer);
    }

    /**
     * @Route ("/export/business_types", name="business_types_export" )
     */
    public
    function businessTypesExport(BusinessTypesRepository $businessTypesRepository)
    {
        $data = [];
        $exported_date = new \DateTime('now');
        $exported_date_formatted = $exported_date->format('d-M-Y');
        $exported_date_formatted_for_file = $exported_date->format('d-m-Y');
        $fileName = 'business_types_export_' . $exported_date_formatted_for_file . '.csv';

        $count = 0;
        $business_types_list = $businessTypesRepository->findAll();
        foreach ($business_types_list as $business_type) {
            $data[] = [
                $business_type->getRanking(),
                $business_type->getBusinessType(),
                $business_type->getDescription(),
                $business_type->getMapIcon(),
                $business_type->getMapIconColour(),
                $business_type->getMapDisplay(),
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Business Types');
        $sheet->getCell('A1')->setValue('Ranking');
        $sheet->getCell('B1')->setValue('Business Type');
        $sheet->getCell('C1')->setValue('Description');
        $sheet->getCell('D1')->setValue('Map Icon');
        $sheet->getCell('E1')->setValue('Map Icon Colour');
        $sheet->getCell('F1')->setValue('Map Display');

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
     * @Route ("/import/business_types", name="business_types_import" )
     */
    public
    function businessTypesImport(Request $request, SluggerInterface $slugger, BusinessTypesRepository $businessTypesRepository, BusinessTypesImportService $businessTypesImportService): Response
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
                        $this->getParameter('business_types_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $businessTypesImportService->importBusinessTypes($newFilename);
                return $this->redirectToRoute('business_types_index');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'Business Types Import',
        ]);
    }
}

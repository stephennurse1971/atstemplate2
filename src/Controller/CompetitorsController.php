<?php

namespace App\Controller;

use App\Entity\Competitors;
use App\Form\CompetitorsType;
use App\Repository\CmsCopyRepository;
use App\Repository\CompetitorServiceRepository;
use App\Repository\CompetitorsRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

use App\Form\ImportType;
use App\Services\ImportCompetitorsService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

/**
 * @Route("/competitors")
 */
class CompetitorsController extends AbstractController
{
    /**
     * @Route("/index", name="competitors_index", methods={"GET"})
     */
    public function index(CompetitorsRepository $competitorsRepository, CompetitorServiceRepository $competitorServiceRepository): Response
    {
        return $this->render('competitors/index.html.twig', [
            'competitors' => $competitorsRepository->findAll(),
            'competitor_services' => $competitorServiceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="competitors_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CompetitorsRepository $competitorsRepository): Response
    {
        $competitor = new Competitors();
        $form = $this->createForm(CompetitorsType::class, $competitor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competitorsRepository->add($competitor, true);

            return $this->redirectToRoute('competitors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('competitors/new.html.twig', [
            'competitor' => $competitor,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/show/{id}", name="competitors_show", methods={"GET"})
     */
    public function show(Competitors $competitor): Response
    {
        return $this->render('competitors/show.html.twig', [
            'competitor' => $competitor,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="competitors_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Competitors $competitor, CompetitorsRepository $competitorsRepository): Response
    {
        $form = $this->createForm(CompetitorsType::class, $competitor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competitorsRepository->add($competitor, true);

            return $this->redirectToRoute('competitors_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('competitors/edit.html.twig', [
            'competitor' => $competitor,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="competitors_delete", methods={"POST"})
     */
    public function delete(Request $request, Competitors $competitor, CompetitorsRepository $competitorsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$competitor->getId(), $request->request->get('_token'))) {
            $competitorsRepository->remove($competitor, true);
        }

        return $this->redirectToRoute('competitors_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/competitors_delete_all", name="competitors_delete_all",)
     */
    public function deleteCmsCopyAllFiles(Request $request, CompetitorsRepository $competitorsRepository, EntityManagerInterface $entityManager): Response
    {
        $referer = $request->server->get('HTTP_REFERER');
        $competitors = $competitorsRepository->findAll();

        foreach ($competitors as $competitor) {
            $entityManager->remove($competitor);
            $entityManager->flush();
        }
        return $this->redirect($referer);
    }

    /**
     * @Route ("/export", name="competitors_export" )
     */
    public function exportCompetitors(CompetitorsRepository $competitorsRepository)
    {
        $data = [];
        $exported_date = new \DateTime('now');
        $exported_date_formatted = $exported_date->format('d-M-Y');
        $exported_date_formatted_for_file = $exported_date->format('d-m-Y');
        $fileName = 'competitors_export_' . $exported_date_formatted_for_file . '.csv';

        $count = 0;
        $competitors_list = $competitorsRepository->findAll();
        foreach ($competitors_list as $competitor) {
            $data[] = [
                "Competitors",
                $competitor->getName(),
                $competitor->getType(),
                $competitor->getTelephone(),
                $competitor->getWebSite(),
                $competitor->getCompetitorAddressStreet(),
                $competitor->getCompetitorAddressCity(),
                $competitor->getCompetitorAddressPostalCode(),
                $competitor->getCompetitorAddressCountry(),
                $competitor->getCompetitorAddressLongitude(),
                $competitor->getCompetitorAddressLatitude(),
                $competitor->getLinkedIn(),
                $competitor->getFacebook(),
                $competitor->getInstagram(),
                $competitor->getTwitter(),
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Business Types');
        $sheet->getCell('A1')->setValue('Entity');
        $sheet->getCell('B1')->setValue('Name');
        $sheet->getCell('C1')->setValue('Business Type');
        $sheet->getCell('D1')->setValue('Telephone');
        $sheet->getCell('E1')->setValue('Website');
        $sheet->getCell('F1')->setValue('Address Street');
        $sheet->getCell('G1')->setValue('Address City');
        $sheet->getCell('H1')->setValue('Address Post Code');
        $sheet->getCell('I1')->setValue('Address Country');
        $sheet->getCell('J1')->setValue('Longitude');
        $sheet->getCell('K1')->setValue('Latitude');
        $sheet->getCell('L1')->setValue('LinkedIn');
        $sheet->getCell('M1')->setValue('Facebook');
        $sheet->getCell('N1')->setValue('Instagram');
        $sheet->getCell('O1')->setValue('Twitter');

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
     * @Route ("/import", name="competitors_import" )
     */
    public function businessTypesImport(Request $request, SluggerInterface $slugger, CompetitorsRepository $competitorsRepository, ImportCompetitorsService $competitorsImportService): Response
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
                        $this->getParameter('competitors_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $competitorsImportService->importCompetitors($newFilename);
                return $this->redirectToRoute('competitors_index');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'Competitors Import',
        ]);
    }
}

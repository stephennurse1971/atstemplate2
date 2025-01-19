<?php

namespace App\Controller;

use App\Entity\MapIcons;
use App\Form\ImportType;
use App\Form\MapIconsType;
use App\Repository\MapIconsRepository;
use App\Services\MapIconsImportService;
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

#[Route('/map/icons')]
class MapIconsController extends AbstractController
{
    #[Route('/index', name: 'map_icons_index', methods: ['GET'])]
    public function index(MapIconsRepository $mapIconsRepository): Response
    {
        return $this->render('map_icons/index.html.twig', [
            'map_icons' => $mapIconsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'map_icons_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MapIconsRepository $mapIconsRepository): Response
    {
        $mapIcon = new MapIcons();
        $form = $this->createForm(MapIconsType::class, $mapIcon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $iconFile = $form->get('iconFile')->getData();
            if ($iconFile) {
                $originalFilename = pathinfo($iconFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '.' . $iconFile->guessExtension();
                try {
                    $iconFile->move(
                        $this->getParameter('business_contacts_map_icon_directory'), // Directory from your services.yaml
                        $newFilename // New unique filename
                    );
                    $mapIcon->setIconFile($newFilename);
                } catch (FileException $e) {
                    die('File upload failed: ' . $e->getMessage());
                }
            }
            $mapIconsRepository->add($mapIcon, true);
            return $this->redirectToRoute('map_icons_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('map_icons/new.html.twig', [
            'map_icon' => $mapIcon,
            'form' => $form,
        ]);
    }


    #[Route('/show/{id}', name: 'map_icons_show', methods: ['GET'])]
    public function show(MapIcons $mapIcon): Response
    {
        return $this->render('map_icons/show.html.twig', [
            'map_icon' => $mapIcon,
        ]);
    }

    #[Route('/edit/{id}', name: 'map_icons_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MapIcons $mapIcon, MapIconsRepository $mapIconsRepository): Response
    {
        $form = $this->createForm(MapIconsType::class, $mapIcon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $iconFile = $form->get('iconFile')->getData();
            if ($iconFile) {
                $originalFilename = pathinfo($iconFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '.' . $iconFile->guessExtension();
                try {
                    $iconFile->move(
                        $this->getParameter('business_contacts_map_icon_directory'), // Directory from your services.yaml
                        $newFilename // New unique filename
                    );
                    $mapIcon->setIconFile($newFilename);
                } catch (FileException $e) {
                    die('File upload failed: ' . $e->getMessage());
                }
            }
            $mapIconsRepository->add($mapIcon, true);
            return $this->redirectToRoute('map_icons_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('map_icons/edit.html.twig', [
            'map_icon' => $mapIcon,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'map_icons_delete', methods: ['POST'])]
    public function delete(Request $request, MapIcons $mapIcon, MapIconsRepository $mapIconsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $mapIcon->getId(), $request->request->get('_token'))) {
            $mapIconsRepository->remove($mapIcon, true);
        }

        return $this->redirectToRoute('map_icons_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/delete_all", name="map_icons_delete_all")
     */
    public function deleteMapIconsAll(MapIconsRepository $mapIconsRepository, EntityManagerInterface $entityManager): Response
    {
        $map_icons = $mapIconsRepository->findAll();
        foreach ($map_icons as $map_icon) {
            $entityManager->remove($map_icon);
            $entityManager->flush();
        }
        return $this->redirectToRoute('map_icons_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete_map_icon_file/{id}", name="map_icon_delete_file", methods={"POST", "GET"})
     */
    public function deleteMapIconFile(Request $request, int $id, MapIcons $mapIcons, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        $fileName = $mapIcons->getIconFile();
        $mapIcons->setIconFile(null);
        $entityManager->flush();
        $files = glob($this->getParameter('map_icon_directory') . $fileName);
        foreach ($files as $file) {
            unlink($file);
        }

        $entityManager->flush();
        return $this->redirect($referer);
    }

    /**
     * @Route ("/export", name="map_icons_export" )
     */
    public function mapIconsExport(MapIconsRepository $mapIconsRepository)
    {
        $data = [];
        $exported_date = new \DateTime('now');
        $exported_date_formatted = $exported_date->format('d-M-Y');
        $fileName = 'map_icons_export_' . $exported_date_formatted . '.csv';

        $count = 0;
        $map_icons_list = $mapIconsRepository->findAll();
        $concatenatedNotes = "Exported on: " . $exported_date_formatted;
        foreach ($map_icons_list as $map_icon) {
            $data[] = [
                $map_icon->getName(),
                $map_icon->getIconFile(),
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Map Icons');
        $sheet->getCell('A1')->setValue('Name');
        $sheet->getCell('B1')->setValue('FileName');

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
     * @Route ("/import", name="map_icons_import" )
     */
    public function mapIconsImport(Request $request, SluggerInterface $slugger, MapIconsRepository $mapIconsRepository, MapIconsImportService $mapIconsImportService): Response
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
                        $this->getParameter('map_icons_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $mapIconsImportService->importMapIcons($newFilename);
                return $this->redirectToRoute('map_icons_index');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'Map Icons',
        ]);
    }

}

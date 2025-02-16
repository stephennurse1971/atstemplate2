<?php

namespace App\Controller;

use App\Entity\UsefulLinks;
use App\Form\ImportType;
use App\Form\UsefulLinksType;
use App\Repository\MapIconsRepository;
use App\Repository\UsefulLinksRepository;
use App\Repository\UserRepository;
use App\Services\CountAllocatedWebsites;
use App\Services\ImportUsefulLinksService;
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
 * @Route("/useful_links")
 */
class UsefulLinksController extends AbstractController
{
    /**
     * @Route("/index/{category}", name="useful_links_index", methods={"GET"})
     */
    public function index(Request $request, string $category, UsefulLinksRepository $usefulLinksRepository, UserRepository $userRepository, CountAllocatedWebsites $countAllocatedWebsites): Response
    {
        $sn = $userRepository->findOneBy([
            'email' => 'nurse_stephen@hotmail.com'
        ]);

        $categories = $usefulLinksRepository->findUniqueCategories();
        $categoryNames = array_map(function ($category) {
            return $category['category'];
        }, $categories);

        $useful_links = $usefulLinksRepository->findAll();

        return $this->render('useful_links/index.html.twig', [
            'sn' => $sn,
            'useful_links' => $useful_links,
            'categories' => $categoryNames,  // Pass the dynamic list of categories
            'category_chosen' => $category
        ]);
    }

    /**
     * @Route("/new", name="useful_links_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UsefulLinksRepository $usefulLinksRepository): Response
    {
        $usefulLink = new UsefulLinks();
        $form = $this->createForm(UsefulLinksType::class, $usefulLink);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usefulLinksRepository->add($usefulLink);
            return $this->redirectToRoute('useful_links_index', ['category' => 'All'], Response::HTTP_SEE_OTHER);
        }

        return $this->render('useful_links/new.html.twig', [
            'useful_link' => $usefulLink,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="useful_links_show", methods={"GET"})
     */
    public function show(UsefulLinks $usefulLink): Response
    {
        return $this->render('useful_links/show.html.twig', [
            'useful_link' => $usefulLink,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="useful_links_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, UsefulLinks $usefulLink, UsefulLinksRepository $usefulLinksRepository): Response
    {
        $form = $this->createForm(UsefulLinksType::class, $usefulLink);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usefulLinksRepository->add($usefulLink);
            return $this->redirectToRoute('useful_links_index', ['category' => 'All'], Response::HTTP_SEE_OTHER);
        }

        return $this->render('useful_links/edit.html.twig', [
            'useful_link' => $usefulLink,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="useful_links_delete", methods={"POST"})
     */
    public function delete(Request $request, UsefulLinks $usefulLink, UsefulLinksRepository $usefulLinksRepository, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $usefulLink->getId(), $request->request->get('_token'))) {
            $entityManager->remove($usefulLink);
            $entityManager->flush();
        }

        return $this->redirectToRoute('useful_links_index', ['category' => 'All'], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route ("/export", name="useful_links_export" )
     */
    public function usefulLinksExport(UsefulLinksRepository $usefulLinksRepository)
    {
        $data = [];
        $exported_date = new \DateTime('now');
        $exported_date_formatted = $exported_date->format('d-M-Y');
        $fileName = 'useful_links_export_' . $exported_date_formatted . '.csv';

        $count = 0;
        $useful_links_list = $usefulLinksRepository->findAll();
        $concatenatedNotes = "Exported on: " . $exported_date_formatted;
        foreach ($useful_links_list as $useful_link) {
            $data[] = [
                'UsefulLinks',
                $useful_link->getCategory(),
                $useful_link->getName(),
                $useful_link->getLink(),
                $useful_link->getNotes(),
                $useful_link->getLogin(),
                $useful_link->getPassword(),
                $useful_link->getPrivate(),
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Useful Links');
        $sheet->getCell('A1')->setValue('Entity');
        $sheet->getCell('B1')->setValue('Category');
        $sheet->getCell('C1')->setValue('Name');
        $sheet->getCell('D1')->setValue('Link');
        $sheet->getCell('E1')->setValue('Notes');
        $sheet->getCell('F1')->setValue('Login');
        $sheet->getCell('G1')->setValue('Password');
        $sheet->getCell('H1')->setValue('Private');

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
     * @Route ("/import", name="useful_links_import" )
     */
    public function mapIconsImport(Request $request, SluggerInterface $slugger, UsefulLinksRepository $usefulLinksRepository, ImportUsefulLinksService $importUsefulLinksService): Response
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
                        $this->getParameter('useful_links_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $importUsefulLinksService->importUsefulLink($newFilename);
                return $this->redirectToRoute('useful_links_index', ['category' => 'All'], Response::HTTP_SEE_OTHER );
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'Useful Links',
        ]);
    }

    /**
     * @Route("/delete_all", name="map_icons_delete_all")
     */
    public function deleteUsefulLinksAll(UsefulLinksRepository $usefulLinksRepository, EntityManagerInterface $entityManager): Response
    {
        $useful_links = $usefulLinksRepository->findAll();
        foreach ($useful_links as $useful_link) {
            $entityManager->remove($useful_link);
            $entityManager->flush();
        }
        return $this->redirectToRoute('useful_links_index', ['category' => 'All'], Response::HTTP_SEE_OTHER );
    }

}




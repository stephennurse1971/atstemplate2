<?php

namespace App\Controller;

use App\Entity\CmsCopyPageFormats;
use App\Form\CmsCopyPageFormatsType;
use App\Form\ImportType;
use App\Repository\CmsCopyPageFormatsRepository;
use App\Services\ImportCmsPageCopyPageFormatService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * @Route("/cms_copy_page_formats")
 * @Security("is_granted('ROLE_ADMIN')")
 */



class CmsCopyPageFormatsController extends AbstractController
{
    #[Route('/index', name: 'cms_copy_page_formats_index', methods: ['GET'])]
    public function index(CmsCopyPageFormatsRepository $cmsCopyPageFormatsRepository): Response
    {
        return $this->render('cms_copy_page_formats/index.html.twig', [
            'cms_copy_page_formats' => $cmsCopyPageFormatsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'cms_copy_page_formats_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CmsCopyPageFormatsRepository $cmsCopyPageFormatsRepository): Response
    {
        $cmsCopyPageFormat = new CmsCopyPageFormats();
        $form = $this->createForm(CmsCopyPageFormatsType::class, $cmsCopyPageFormat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cmsCopyPageFormatsRepository->add($cmsCopyPageFormat, true);

            return $this->redirectToRoute('cms_copy_page_formats_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cms_copy_page_formats/new.html.twig', [
            'cms_copy_page_format' => $cmsCopyPageFormat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'cms_copy_page_formats_show', methods: ['GET'])]
    public function show(CmsCopyPageFormats $cmsCopyPageFormat): Response
    {
        return $this->render('cms_copy_page_formats/show.html.twig', [
            'cms_copy_page_format' => $cmsCopyPageFormat,
        ]);
    }

    #[Route('/edit/{id}', name: 'cms_copy_page_formats_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CmsCopyPageFormats $cmsCopyPageFormat, CmsCopyPageFormatsRepository $cmsCopyPageFormatsRepository): Response
    {
        $form = $this->createForm(CmsCopyPageFormatsType::class, $cmsCopyPageFormat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cmsCopyPageFormatsRepository->add($cmsCopyPageFormat, true);

            return $this->redirectToRoute('cms_copy_page_formats_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cms_copy_page_formats/edit.html.twig', [
            'cms_copy_page_format' => $cmsCopyPageFormat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'cms_copy_page_formats_delete', methods: ['POST'])]
    public function delete(Request $request, CmsCopyPageFormats $cmsCopyPageFormat, CmsCopyPageFormatsRepository $cmsCopyPageFormatsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cmsCopyPageFormat->getId(), $request->request->get('_token'))) {
            $cmsCopyPageFormatsRepository->remove($cmsCopyPageFormat, true);
        }

        return $this->redirectToRoute('cms_copy_page_formats_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/delete_all", name="cms_copy_page_formats_delete_all")
     */
    public function deleteAllCMSCopyPageFormats(CmsCopyPageFormatsRepository $cmsCopyPageFormatsRepository, EntityManagerInterface $entityManager): Response
    {
        $cms_copy_page_formats = $cmsCopyPageFormatsRepository->findAll();
        foreach ($cms_copy_page_formats as $cms_copy_page_format) {
            $entityManager->remove($cms_copy_page_format);
            $entityManager->flush();
        }
        return $this->redirectToRoute('cms_copy_page_formats_index', [], Response::HTTP_SEE_OTHER);
    }



    /**
     * @Route ("/export/CmsCopyPageFormats", name="cms_copy_page_formats_export" )
     */
    public
    function cmsCopypageFormatsExport(CmsCopyPageFormatsRepository $cmsCopyPageFormatsRepository)
    {
        $data = [];
        $exported_date = new \DateTime('now');
        $exported_date_formatted = $exported_date->format('d-m-Y');
        $fileName = 'cms_copy_page_formats_export_' . $exported_date_formatted . '.csv';

        $count = 0;
        $cms_copy_page_formats_list = $cmsCopyPageFormatsRepository->findAll();
        foreach ($cms_copy_page_formats_list as $cms_copy_page_format) {
            $data[] = [
                'CMSCopyPageFormats',
                $cms_copy_page_format->getName(),
                $cms_copy_page_format->getDescription(),
                $cms_copy_page_format->getUses(),
                $cms_copy_page_format->getPros(),
                $cms_copy_page_format->getCons(),
                $cms_copy_page_format->getCode(),
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('CMS Copy Page Formats');
        $sheet->getCell('A1')->setValue('Entity');
        $sheet->getCell('B1')->setValue('Name');
        $sheet->getCell('C1')->setValue('Description');
        $sheet->getCell('D1')->setValue('Uses');
        $sheet->getCell('E1')->setValue('Pros');
        $sheet->getCell('F1')->setValue('Cons');
        $sheet->getCell('G1')->setValue('Code');

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
     * @Route ("/import/CmsCopyPageFormats", name="cms_copy_page_formats_import" )
     */
    public
    function cmsFormatsImport(Request $request, SluggerInterface $slugger, CmsCopyPageFormatsRepository $cmsCopyPageFormatsRepository, ImportCmsPageCopyPageFormatService $cmsPageCopyPageFormatImportService): Response
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
                        $this->getParameter('cms_copy_page_formats_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $cmsPageCopyPageFormatImportService->importCmsCopyPageFormats($newFilename);
                return $this->redirectToRoute('cms_copy_page_formats_index');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'CMS Copy Page Formats Import',
        ]);
    }

}

<?php

namespace App\Controller;

use App\Entity\CmsCopy;
use App\Form\CmsCopyType;
use App\Form\ImportType;
use App\Repository\CmsCopyRepository;
use App\Repository\PhotoLocationsRepository;
use App\Repository\ProductRepository;
use App\Services\ImportCMSCopyService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/cmscopy")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class CmsCopyController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private CmsCopyRepository $cmsCopyRepository;

    public function __construct(EntityManagerInterface $entityManager, PhotoLocationsRepository $photoLocationsRepository)
    {
        $this->entityManager = $entityManager;
        $this->photoLocationsRepository = $photoLocationsRepository;
    }


    /**
     * @Route("/index", name="cms_copy_index", methods={"GET"})
     */
    public function index(CmsCopyRepository $cmsCopyRepository, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('cms_copy/index.html.twig', [
            'cms_copies' => $cmsCopyRepository->findAll(),
            'products' => $products
        ]);
    }

    /**
     * @Route("/new", name="cms_copy_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $cmsCopy = new CmsCopy();
        $form = $this->createForm(CmsCopyType::class, $cmsCopy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $attachment = $form->get('attachment')->getData();
            if ($attachment) {
                $originalFilename = pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME);
                if ($cmsCopy->getProduct()) {
                    $safeFilename = $cmsCopy->getProduct()->getProduct() . uniqid();
                }
                if ($cmsCopy->getStaticPageName()) {
                    $safeFilename = $cmsCopy->getStaticPageName() . uniqid();
                }
                $newFilename = $safeFilename . '.' . $attachment->guessExtension();
                try {
                    $attachment->move(
                        $this->getParameter('cms_copy_attachments_directory'),
                        $newFilename
                    );
                    $cmsCopy->setAttachment($newFilename);
                } catch (FileException $e) {
                    die('Import failed');
                }
            }
            if ($cmsCopy->getCategory() == "ProductService") {
                $cmsCopy->setStaticPageName(null);
            }
            if ($cmsCopy->getCategory() == "Static") {
                $cmsCopy->setProduct(null);
            }
            $entityManager = $this->entityManager;
            $entityManager->persist($cmsCopy);
            $entityManager->flush();
            return $this->redirectToRoute('cms_copy_index');
        }

        return $this->render('cms_copy/new.html.twig', [
            'cms_copy' => $cmsCopy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="cms_copy_show", methods={"GET"})
     */
    public function show(CmsCopy $cmsCopy): Response
    {
        return $this->render('cms_copy/show.html.twig', [
            'cms_copy' => $cmsCopy,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="cms_copy_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CmsCopy $cmsCopy): Response
    {
        $form = $this->createForm(CmsCopyType::class, $cmsCopy);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $attachment = $form->get('attachment')->getData();
            if ($attachment) {
                $originalFilename = pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '.' . $attachment->guessExtension();
                try {
                    $attachment->move(
                        $this->getParameter('cms_copy_attachments_directory'),
                        $newFilename
                    );
                    $cmsCopy->setAttachment($newFilename);
                } catch (FileException $e) {
                    die('Import failed');
                }
            }
            if ($cmsCopy->getCategory() == "ProductService") {
                $cmsCopy->setStaticPageName(null);
            }
            if ($cmsCopy->getCategory() == "Static") {
                $cmsCopy->setProduct(null);
            }
            $entityManager = $this->entityManager;
            $entityManager->persist($cmsCopy);
            $entityManager->flush();
            return $this->redirectToRoute('cms_copy_index');
        }

        return $this->render('cms_copy/edit.html.twig', [
            'cms_copy' => $cmsCopy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/copy_and_edit/{id}", name="cms_copy_copy_and_edit", methods={"GET","POST"})
     */
    public function copyAndEdit(Request $request, CmsCopy $cmsCopy, EntityManagerInterface $entityManager): Response
    {
        $product = $cmsCopy->getProduct();
        $sitePage = 'Test';
        $cmsCopy = new CmsCopy();
        $cmsCopy->setProduct($product)
            ->setContentText($sitePage . ' - Content text')
            ->setContentTitle($sitePage . ' - Title text')
            ->setContentTextFR($sitePage . ' - Content text (FR)')
            ->setContentTitleFR($sitePage . ' - Title text (FR)')
            ->setContentTextDE($sitePage . ' - Content text (DE)')
            ->setContentTitleDE($sitePage . ' - Title text (DE)');
        $form = $this->createForm(CmsCopyType::class, $cmsCopy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->entityManager;
            $entityManager->persist($cmsCopy);
            $entityManager->flush();
            return $this->redirectToRoute('cms_copy_index');
        }

        return $this->render('cms_copy/new.html.twig', [
            'cms_copy' => $cmsCopy,
            'form' => $form->createView(),
        ]);

    }


    /**
     * @Route("/delete/{id}", name="cms_copy_delete", methods={"POST"})
     */
    public function delete(Request $request, CmsCopy $cmsCopy, EntityManagerInterface $entityManager): Response
    {
        $referer = $request->headers->get('referer');
        $fileName = $cmsCopy->getAttachment();
        $file = $this->getParameter('cms_copy_attachments_directory') . $fileName;
        if (file_exists($file)) {
            unlink($file);
        }
        $cmsCopy->setAttachment(null);
        $entityManager->flush();

        if ($this->isCsrfTokenValid('delete' . $cmsCopy->getId(), $request->request->get('_token'))) {
            $entityManager = $this->entityManager;
            $entityManager->remove($cmsCopy);
            $entityManager->flush();
        }
        return $this->redirect($referer);
    }

    /**
     * @Route("/show_attachment/{id}", name="cms_copy_show_attachment")
     */
    public function showCmsCopyAttachment(Request $request, CmsCopy $cmsCopy)
    {
        $filename = $cmsCopy->getAttachment();
        $filepath = $this->getParameter('cms_copy_attachments_directory') . $filename;
        if (file_exists($filepath)) {
            $response = new BinaryFileResponse($filepath);
            //  $response->headers->set('Content-Type');
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE, //use ResponseHeaderBag::DISPOSITION_ATTACHMENT to save as an attachment
                $filename
            );
            return $response;
        } else {
            return new Response("file does not exist");
        }
    }

    /**
     * @Route("/cms_copy_delete_file/{id}", name="cms_copy_delete_file", methods={"POST", "GET"})
     */
    public function deleteCmsCopyFile(int $id, Request $request, CmsCopy $cmsCopy, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        $fileName = $cmsCopy->getAttachment();
        $file = $this->getParameter('cms_copy_attachments_directory') . $fileName;
        if (file_exists($file)) {
            unlink($file);
        }
        $cmsCopy->setAttachment(null);
        $entityManager->flush();
        return $this->redirect($referer);
    }


    /**
     * @Route("/cms_copy_delete/all_files", name="cms_copy_delete_all_files",)
     */
    public function deleteCmsCopyAllFiles(Request $request, CmsCopyRepository $cmsCopyRepository, EntityManagerInterface $entityManager): Response
    {
        $referer = $request->server->get('HTTP_REFERER');
        $cms_copys = $cmsCopyRepository->findAll();

        $files = glob($this->getParameter('cms_copy_attachments_directory') . "*");
        foreach ($files as $file) {
            unlink($file);
        }
        $entityManager->flush();

        foreach ($cms_copys as $cms_copy) {
            $cms_copy->setAttachment(null);
            $entityManager->flush();
        }
        return $this->redirect($referer);
    }


    /**
     * @Route("/cms_copy_reset_counters", name="cms_copy_reset_counters",)
     */
    public function resetCounters(Request $request, CmsCopyRepository $cmsCopyRepository, EntityManagerInterface $entityManager): Response
    {
        $referer = $request->server->get('HTTP_REFERER');
        $cms_copys = $cmsCopyRepository->findAll();

        foreach ($cms_copys as $cms_copy) {
            $cms_copy->setPageCountAdmin(null);
            $cms_copy->setPageCountUsers(null);
            $entityManager->flush();
        }
        return $this->redirect($referer);
    }





    /**
     * @Route ("/export", name="cms_copy_export")
     */
    public function cmsCopyExport(CmsCopyRepository $cmsCopyRepository)
    {
        $exportedDate = new \DateTime('now');
        $exportedDateFormatted = $exportedDate->format('d-M-Y');
        $fileName = 'cms_copy_export_' . $exportedDateFormatted . '.csv';

        $cmsCopyList = $cmsCopyRepository->findAll();
        $data = [];
        $data[] = [ // Headers
            'Entity', 'Category', 'Product', 'Ranking', 'Hyperlink', 'Attachment',
            'PageCountUsers', 'PageCountAdmin', 'PageLayout', 'TabTitle', 'TabTitleFR',
            'TabTitleDE', 'ContentTitle', 'ContentTitleFR', 'ContentTitleDE',
            'ContentText', 'ContentTextFR', 'ContentTextDE',
        ];

        foreach ($cmsCopyList as $cmsCopy) {
            $product = ($cmsCopy->getCategory() === 'Product or Service' && $cmsCopy->getProduct())
                ? $cmsCopy->getProduct()->getProduct()
                : 'NA';

            $data[] = [
                'CMSCopy',
                $cmsCopy->getCategory(),
                $product,
                $cmsCopy->getRanking(),
                $cmsCopy->getHyperlinks(),
                $cmsCopy->getAttachment(),

                $cmsCopy->getPageCountUsers(),
                $cmsCopy->getPageCountAdmin(),
                $cmsCopy->getPageLayout()->getName(),

                $cmsCopy->getTabTitle(),
                $cmsCopy->getTabTitleFR(),
                $cmsCopy->getTabTitleDE(),

                $cmsCopy->getContentTitle(),
                $cmsCopy->getContentTitleFR(),
                $cmsCopy->getContentTitleDE(),

                $cmsCopy->getContentText(),
                $cmsCopy->getContentTextFR(),
                $cmsCopy->getContentTextDE(),
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($data, null, 'A1', true);

        // Add hyperlink to a column (L)
        $totalRows = $sheet->getHighestRow();
        for ($i = 2; $i <= $totalRows; $i++) {
            $cell = "L" . $i;
            $sheet->getCell($cell)->getHyperlink()->setUrl("https://google.com");
        }

        $writer = new Csv($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        ));
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * @Route ("/import", name="cms_copy_import" )
     */
    public function productImport(Request $request, SluggerInterface $slugger, ImportCMSCopyService $importCMSCopyService): Response
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
                        $this->getParameter('cms_copy_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $importCMSCopyService->importCMSCopy($newFilename);
                return $this->redirectToRoute('cms_copy_index');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'CMS Copy',
        ]);
    }


}

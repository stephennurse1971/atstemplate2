<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ImportType;
use App\Form\ProductType;
use App\Repository\CmsCopyRepository;
use App\Repository\ProductRepository;
use App\Services\ImportProductsService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/product")
 * @Security("is_granted('ROLE_ADMIN')")
 */

class ProductController extends AbstractController
{
    /**
     * @Route("/index", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product, true);
            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="product_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, ProductRepository $productRepository, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $session->getFlashBag()->clear();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product, true);

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, ProductRepository $productRepository, CmsCopyRepository $cmsCopyRepository): Response
    {
        $relatedRecords = $cmsCopyRepository->findBy(['product' => $product]);
        if (count($relatedRecords) > 0) {
            $this->addFlash('error', 'Cannot delete this product because it is referenced by other records.');
            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            try {
                $productRepository->remove($product, true);
                $this->addFlash('success', 'Product deleted successfully.');
            } catch (ForeignKeyConstraintViolationException $e) {
                $this->addFlash('error', 'Unable to delete product due to foreign key constraints.');
            }
        }
        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/change_ranking/{change}/{id}", name="product_change_ranking", methods={"GET", "POST"})
     */
    public function changePriority(Request $request, $change, Product $product, EntityManagerInterface $manager): Response
    {
        $referer = $request->headers->get('Referer');
        $currentRanking = $product->getRanking();
        if ($change == "Up") {
            $newRanking = $currentRanking - 1.01;
        }
        if ($change == "Down") {
            $newRanking = $currentRanking + 1.01;
        }
        $product->setRanking($newRanking);
        $manager->flush();
        return $this->redirect($referer);
    }

    /**
     * @Route("/change_status/{input}/{status}/{id}", name="product_change_status", methods={"GET", "POST"})
     */
    public function changeStatus(Request $request, $input, $status, Product $product, EntityManagerInterface $manager): Response
    {
        $referer = $request->headers->get('Referer');
        if ($input == 'isActive') {
            if ($status == "Yes") {
                $product->setIsActive('1');
            }
            if ($status == "No") {
                $product->setIsActive('0');
            }
        }
        if ($input == 'includeInFooter') {
            if ($status == "Yes") {
                $product->setIncludeInFooter('1');
            }
            if ($status == "No") {
                $product->setIncludeInFooter('0');
            }
        }
        if ($input == 'includeInContactForm') {
            if ($status == "Yes") {
                $product->setIncludeInContactForm('1');
            }
            if ($status == "No") {
                $product->setIncludeInContactForm('0');
            }
        }
        if ($input == 'main_sub') {
            if ($status == "Main") {
                $product->setCategory('Main');
            }
            if ($status == "Sub") {
                $product->setCategory('Sub');
            }
        }
        $manager->flush();
        return $this->redirect($referer);
    }

    /**
     * @Route("/delete_all", name="products_delete_all")
     */
    public function deleteAllFacebookGroups(ProductRepository $productRepository, EntityManagerInterface $entityManager): Response
    {
        $products = $productRepository->findAll();
        foreach ($products as $product) {
            $entityManager->remove($product);
            $entityManager->flush();
        }
        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route ("/export", name="product_export" )
     */
    public function productsExport(ProductRepository $productRepository)
    {
        $data = [];
        $exported_date = new \DateTime('now');
        $exported_date_formatted = $exported_date->format('d-M-Y');
        $fileName = 'products_export_' . $exported_date_formatted . '.csv';

        $count = 0;
        $products_list = $productRepository->findAll();
        $concatenatedNotes = "Exported on: " . $exported_date_formatted;
        foreach ($products_list as $product) {
            $data[] = [
                'Products',
                $product->getRanking(),
                $product->getCategory(),
                $product->getProduct(),
                $product->isIsActive(),
                $product->getIncludeInFooter(),
                $product->isIncludeInContactForm(),
                $product->getNotes(),
                $product->getNewClientEmail(),
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products');
        $sheet->getCell('A1')->setValue('Products');
        $sheet->getCell('B1')->setValue('Ranking');
        $sheet->getCell('C1')->setValue('Category');
        $sheet->getCell('D1')->setValue('Product');
        $sheet->getCell('E1')->setValue('Is Active');
        $sheet->getCell('F1')->setValue('Include in Footer');
        $sheet->getCell('G1')->setValue('Include in Contact Form');
        $sheet->getCell('H1')->setValue('Notes');
        $sheet->getCell('I1')->setValue('New Client Email');

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
     * @Route ("/import", name="product_import" )
     */
    public function productImport(Request $request, SluggerInterface $slugger, ProductRepository $productRepository, ImportProductsService $importProductsService): Response
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
                        $this->getParameter('products_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $importProductsService->importProducts($newFilename);
                return $this->redirectToRoute('product_index');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'Products Import',
        ]);
    }

}



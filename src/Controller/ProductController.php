<?php

namespace App\Controller;

use App\Entity\BusinessTypes;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
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
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
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
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
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


}

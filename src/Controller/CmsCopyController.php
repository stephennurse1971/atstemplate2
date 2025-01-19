<?php

namespace App\Controller;

use App\Entity\CmsCopy;
use App\Form\CmsCopyType;
use App\Repository\CmsCopyRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cmscopy")
 */
class CmsCopyController extends AbstractController
{
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
            if($cmsCopy->getCategory()=="ProductService"){
                $cmsCopy->setStaticPageName(null);
            }
            if($cmsCopy->getCategory()=="Static"){
                $cmsCopy->setProduct(null);
            }
            $entityManager = $this->getDoctrine()->getManager();
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
            if($cmsCopy->getCategory()=="ProductService"){
                $cmsCopy->setStaticPageName(null);
            }
            if($cmsCopy->getCategory()=="Static"){
                $cmsCopy->setProduct(null);
            }
            $entityManager = $this->getDoctrine()->getManager();
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
    public function copyAndEdit(Request $request, CmsCopy $cmsCopy, EntityManagerInterface $manager): Response
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
            $entityManager = $this->getDoctrine()->getManager();
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
    public function delete(Request $request, CmsCopy $cmsCopy): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cmsCopy->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cmsCopy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('cms_copy_index');
    }

    /**
     * @Route("/show_attachment/{id}", name="cms_copy_show_attachment")
     */
    public function showCmsCopyAttachment(Request $request, CmsCopy $cmsCopy)
    {
        $filename = $cmsCopy->getAttachment();
        $filepath = $this->getParameter('cms_copy_attachments_directory') . "/" . $filename;
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
        $file = $this->getParameter('cms_copy_attachments_directory') . "/".$fileName;
        if(file_exists($file)){
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

        $files = glob($this->getParameter('cms_copy_attachments_directory') . "/*");
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



}

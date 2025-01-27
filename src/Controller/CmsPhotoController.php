<?php

namespace App\Controller;

use App\Entity\CmsPhoto;
use App\Form\CmsPhotoType;
use App\Repository\CmsPhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/cmsphoto")
 * @Security("is_granted('ROLE_ADMIN')")
 */


class CmsPhotoController extends AbstractController
{
    /**
     * @Route("/index", name="cms_photo_index", methods={"GET"})
     */
    public function index(CmsPhotoRepository $cmsPhotoRepository): Response
    {
        return $this->render('cms_photo/index.html.twig', [
            'cms_photos' => $cmsPhotoRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="cms_photo_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $cmsPhoto = new CmsPhoto();
        $form = $this->createForm(CmsPhotoType::class, $cmsPhoto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                if ($cmsPhoto->getProduct()) {
                    $safeFilename = $cmsPhoto->getProduct()->getProduct() . uniqid();
                }
                if ($cmsPhoto->getStaticPageName()) {
                    $safeFilename = $cmsPhoto->getStaticPageName() . uniqid();
                }

                $newFilename = $safeFilename . '.' . $photo->guessExtension();
                try {
                    $photo->move(
                        $this->getParameter('cms_photos_directory'),
                        $newFilename
                    );
                    $cmsPhoto->setPhoto($newFilename);
                } catch (FileException $e) {
                    die('Import failed');
                }
            }
            if ($cmsPhoto->getCategory() == "ProductService") {
                $cmsPhoto->setStaticPageName(null);
            }
            if ($cmsPhoto->getCategory() == "Static") {
                $cmsPhoto->setProduct(null);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cmsPhoto);
            $entityManager->flush();

            return $this->redirectToRoute('cms_photo_index');
        }

        return $this->render('cms_photo/new.html.twig', [
            'cms_photo' => $cmsPhoto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="cms_photo_show", methods={"GET"})
     */
    public function show(CmsPhoto $cmsPhoto): Response
    {
        return $this->render('cms_photo/show.html.twig', [
            'cms_photo' => $cmsPhoto,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="cms_photo_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, CmsPhoto $cmsPhoto, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CmsPhotoType::class, $cmsPhoto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                if ($cmsPhoto->getProduct()) {
                    $safeFilename = $cmsPhoto->getProduct()->getProduct() . uniqid();
                }
                if ($cmsPhoto->getStaticPageName()) {
                    $safeFilename = $cmsPhoto->getStaticPageName() . uniqid();
                }
                $newFilename = $safeFilename . '.' . $photo->guessExtension();
                try {
                    $photo->move(
                        $this->getParameter('cms_photos_directory'),
                        $newFilename
                    );
                    $cmsPhoto->setPhoto($newFilename);
                } catch (FileException $e) {
                    die('Import failed');
                }
            }
            if ($cmsPhoto->getCategory() == "ProductService") {
                $cmsPhoto->setStaticPageName(null);
            }
            if ($cmsPhoto->getCategory() == "Static") {
                $cmsPhoto->setProduct(null);
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('cms_photo_index');
        }
        return $this->render('cms_photo/edit.html.twig', [
            'cms_photo' => $cmsPhoto,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/rotate_cms_photo/{id}", name="rotate_cms_photo")
     */
    public function rotatePhoto(Request $request, int $id, CmsPhotoRepository $cmsPhotoRepository, EntityManagerInterface $entityManager)
    {
        $referer = $request->server->get('HTTP_REFERER');
        $cms_photo = $cmsPhotoRepository->find($id);

        if ($cms_photo->getRotate() == null || $cms_photo->getRotate() == 0) {
            $cms_photo->setRotate(90);
        } elseif ($cms_photo->getRotate() == 90) {
            $cms_photo->setRotate(180);
        } elseif ($cms_photo->getRotate() == 180) {
            $cms_photo->setRotate(270);
        } elseif ($cms_photo->getRotate() == 270) {
            $cms_photo->setRotate(0);
        }
        $entityManager->persist($cms_photo);
        $entityManager->flush();
        return $this->redirect($referer);
    }


    /**
     * @Route("/delete/{id}", name="cms_photo_delete", methods={"POST"})
     */
    public function delete(Request $request, CmsPhoto $cmsPhoto): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cmsPhoto->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cmsPhoto);
            $entityManager->flush();
        }
        return $this->redirectToRoute('cms_photo_index');
    }

    /**
     * @Route("/delete_photo_file/{id}", name="cms_photo_file_delete", methods={"POST", "GET"})
     */
    public function deleteCMSPhotoFile(int $id, Request $request, CmsPhoto $cmsPhoto, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        $file_name = $cmsPhoto->getPhoto();
        if ($file_name) {
            $file = $this->getParameter('cms_photos_directory') . $file_name;
            if (file_exists($file)) {
                unlink($file);
            }
            $cmsPhoto->setPhoto('');
            $entityManager->flush();
        }
        return $this->redirect($referer);
    }


    /**
     * @Route ("/view_photo/{id}", name="cms_photo_view")
     */
    public function viewCMSPhoto(int $id, CmsPhotoRepository $cmsPhotoRepository)
    {
        $cms_photo = $cmsPhotoRepository->find($id);
        return $this->render('cms_photo/image_view.html.twig', ['imagename' => $cms_photo]);
    }


    /**
     * @Route("/cms_photos_delete_all_files", name="cms_photos_delete_all_files",)
     */
    public function deleteAll(Request $request, CmsPhotoRepository $cmsPhotoRepository, EntityManagerInterface $entityManager): Response
    {
        $referer = $request->server->get('HTTP_REFERER');
        $cms_photos = $cmsPhotoRepository->findAll();

        $files = glob($this->getParameter('cms_photos_directory') . "/*");
        foreach ($files as $file) {
            unlink($file);
        }
        $entityManager->flush();

        foreach ($cms_photos as $cms_photo) {
            $cms_photo->setPhoto(null);
            $entityManager->flush();
        }
        return $this->redirect($referer);
    }
}

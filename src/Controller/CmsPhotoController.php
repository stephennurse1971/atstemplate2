<?php

namespace App\Controller;

use App\Entity\CmsPhoto;
use App\Form\CmsPhotoType;
use App\Form\ImportType;
use App\Repository\CmsPhotoRepository;
use App\Repository\PhotoLocationsRepository;
use App\Services\ImportCMSPhotoService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/cmsphoto")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class CmsPhotoController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private PhotoLocationsRepository $photoLocationsRepository;

    public function __construct(EntityManagerInterface $entityManager, PhotoLocationsRepository $photoLocationsRepository)
    {
        $this->entityManager = $entityManager;
        $this->photoLocationsRepository = $photoLocationsRepository;
    }


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
    public function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $cmsPhoto = new CmsPhoto();
        $form = $this->createForm(CmsPhotoType::class, $cmsPhoto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $uniqueId = uniqid(); // Generates a unique ID
                $uniqueId3digits = substr($uniqueId, 0, 3);
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                if ($cmsPhoto->getProduct()) {
                    $safeFilename = $cmsPhoto->getProduct()->getProduct() . '_' . $cmsPhoto->getRanking() . '_' . $uniqueId3digits;
                }
                if ($cmsPhoto->getStaticPageName()) {
                    $safeFilename = $cmsPhoto->getStaticPageName() . '_' . $cmsPhoto->getRanking() . '_' . $uniqueId3digits ;
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
            $entityManager = $this->entityManager;
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
    public function edit(Request $request, CmsPhoto $cmsPhoto, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CmsPhotoType::class, $cmsPhoto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $uniqueId = uniqid(); // Generates a unique ID
                $uniqueId3digits = substr($uniqueId, 0, 3); // Extracts the first 3 digits

                if ($cmsPhoto->getProduct()) {
                    $safeFilename = $cmsPhoto->getProduct()->getProduct() . '_' . $cmsPhoto->getRanking() . '_' . $uniqueId;
                }
                if ($cmsPhoto->getStaticPageName()) {
                    $safeFilename = $cmsPhoto->getStaticPageName() . '_' . $cmsPhoto->getRanking() . '_' . $uniqueId;
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
            $entityManager = $this->entityManager;
            $entityManager->persist($cmsPhoto);
            $entityManager->flush();
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
    public function delete(Request $request, CmsPhoto $cmsPhoto, EntityManagerInterface $entityManager): Response
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

        if ($this->isCsrfTokenValid('delete' . $cmsPhoto->getId(), $request->request->get('_token'))) {
            $entityManager = $this->entityManager;
            $entityManager->remove($cmsPhoto);
            $entityManager->flush();
        }
        return $this->redirect($referer);
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
        $rotate = $cms_photo->getRotate();
        return $this->render('cms_photo/image_view.html.twig', [
            'imagename' => $cms_photo,
            'rotate' => $rotate
        ]);
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


    /**
     * @Route("/export", name="cms_photo_export")
     */
    public function cmsPhotoExport(CmsPhotoRepository $cmsPhotoRepository)
    {
        $exportedDate = new \DateTime('now');
        $exportedDateFormatted = $exportedDate->format('d-M-Y');
        $fileName = 'cms_photo_export_' . $exportedDateFormatted . '.csv';

        $cmsPhotoList = $cmsPhotoRepository->findAll();
        $data = [];
        $data[] = [ // Headers
            'Entity', 'Category', 'Product', 'Static Page Name', 'Ranking', 'Title', 'Link', 'PhotoOrVideo', 'Rotate', 'Photo'
        ];

        foreach ($cmsPhotoList as $cmsPhoto) {
            // Check if the Product relationship exists
            $product = ($cmsPhoto->getCategory() === 'Product or Service' && $cmsPhoto->getProduct() !== null)
                ? $cmsPhoto->getProduct()->getProduct()
                : 'NA';

            $data[] = [
                'CMSPhoto',
                $cmsPhoto->getCategory(),
                $product, // Use the determined product value
                $cmsPhoto->getStaticPageName(),
                $cmsPhoto->getRanking(),
                $cmsPhoto->getTitle(),
                $cmsPhoto->getLink(),
                $cmsPhoto->getPhotoOrVideo(),
                $cmsPhoto->getRotate(),
                $cmsPhoto->getPhoto(),
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
     * @Route ("/import", name="cms_photo_import" )
     */
    public function cmsPhotoImport(Request $request, SluggerInterface $slugger, ImportCMSPhotoService $importCMSPhotoService): Response
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
                        $this->getParameter('cms_photo_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $importCMSPhotoService->importCMSPhoto($newFilename);
                return $this->redirectToRoute('cms_photo_index');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'CMS Photo',
        ]);
    }


}

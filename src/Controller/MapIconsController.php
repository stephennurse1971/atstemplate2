<?php

namespace App\Controller;

use App\Entity\MapIcons;
use App\Form\MapIconsType;
use App\Repository\MapIconsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
                        $this->getParameter('map_icon_directory'), // Directory from your services.yaml
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
                        $this->getParameter('map_icon_directory'), // Directory from your services.yaml
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


}

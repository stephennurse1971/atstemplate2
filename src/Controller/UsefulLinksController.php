<?php

namespace App\Controller;

use App\Entity\UsefulLinks;
use App\Form\UsefulLinksType;
use App\Repository\UsefulLinksRepository;
use App\Repository\UserRepository;
use App\Services\CountAllocatedWebsites;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $categories = ['ATS', 'Finance', 'Health', 'Cyprus Estate Agent', 'Other Estate Agent',
            'Shopping', 'Gwenny', 'AX', 'IT', 'Recruitment', 'RT'];
        $useful_links = $usefulLinksRepository->findAll();

        return $this->render('useful_links/index.html.twig', [
            'sn' => $sn,
            'useful_links' => $useful_links,
            'categories' => $categories,
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
}




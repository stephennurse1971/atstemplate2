<?php

namespace App\Controller;

use App\Entity\SubPage;
use App\Form\SubPageType;
use App\Repository\SubPageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sub_page")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class SubPageController extends AbstractController
{
    /**
     * @Route("/index", name="sub_page_index", methods={"GET"})
     */
    public function index(SubPageRepository $subPageRepository): Response
    {
        return $this->render('sub_page/index.html.twig', [
            'sub_pages' => $subPageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="sub_page_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SubPageRepository $subPageRepository): Response
    {
        $sub_page = new SubPage();
        $form = $this->createForm(SubPageType::class, $sub_page);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $subPageRepository->add($sub_page, true);

            return $this->redirectToRoute('sub_page_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('sub_page/new.html.twig', [
            'sub_page' => $sub_page,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="sub_page_show", methods={"GET"})
     */
    public function show(SubPage $subPage): Response
    {
        return $this->render('sub_page/show.html.twig', [
            'sub_page' => $subPage,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="sub_page_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, SubPage $subPage, SubPageRepository $subPageRepository): Response
    {
        $form = $this->createForm(SubPageType::class ,$subPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $subPageRepository->add($subPage, true);

            return $this->redirectToRoute('sub_page_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('sub_page/edit.html.twig', [
            'sub_page' => $subPage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="sub_page_delete", methods={"POST"})
     */
    public function delete(Request $request, SubPage $subPage, SubPageRepository $subPageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subPage->getId(), $request->request->get('_token'))) {
            $subPageRepository->remove($subPage, true);
        }
        return $this->redirectToRoute('sub_page_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/copy/{id}", name="sub_page_copy", methods={"GET", "POST"})
     */
    public function copy(Request $request, $id, SubPageRepository $subPageRepository, EntityManagerInterface $manager): Response
    {
        $referer = $request->headers->get('Referer');
        $itemToCopy = $subPageRepository->find($id);
        $content = $itemToCopy->getContent();
        $rank = $itemToCopy->getRank();
        $newCopy = new SubPage();
        $newCopy->setContent($content);
        $newCopy->setRank($rank+1);
        $manager->persist($newCopy);
        $manager->flush();
        return $this->redirect($referer);
    }
}

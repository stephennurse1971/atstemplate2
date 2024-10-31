<?php

namespace App\Controller;

use App\Entity\FacebookGroups;
use App\Form\FacebookGroupsType;
use App\Repository\FacebookGroupsRepository;
use App\Repository\FacebookGroupsReviewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/facebook/groups")
 */
class FacebookGroupsController extends AbstractController
{
    /**
     * @Route("/", name="facebook_groups_index", methods={"GET"})
     */
    public function index(FacebookGroupsRepository $facebookGroupsRepository, FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository): Response
    {
        return $this->render('facebook_groups/index.html.twig', [
            'facebook_groups' => $facebookGroupsRepository->findAll(),
            'facebook_group_reviews' => $facebookGroupsReviewsRepository->findByDateLatest(),
        ]);
    }

    /**
     * @Route("/new", name="facebook_groups_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FacebookGroupsRepository $facebookGroupsRepository): Response
    {
        $facebookGroup = new FacebookGroups();
        $form = $this->createForm(FacebookGroupsType::class, $facebookGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facebookGroupsRepository->add($facebookGroup, true);

            return $this->redirectToRoute('facebook_groups_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facebook_groups/new.html.twig', [
            'facebook_group' => $facebookGroup,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="facebook_groups_show", methods={"GET"})
     */
    public function show(FacebookGroups $facebookGroup): Response
    {
        return $this->render('facebook_groups/show.html.twig', [
            'facebook_group' => $facebookGroup,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="facebook_groups_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, FacebookGroups $facebookGroup, FacebookGroupsRepository $facebookGroupsRepository): Response
    {
        $form = $this->createForm(FacebookGroupsType::class, $facebookGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facebookGroupsRepository->add($facebookGroup, true);

            return $this->redirectToRoute('facebook_groups_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facebook_groups/edit.html.twig', [
            'facebook_group' => $facebookGroup,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="facebook_groups_delete", methods={"POST"})
     */
    public function delete(Request $request, FacebookGroups $facebookGroup, FacebookGroupsRepository $facebookGroupsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facebookGroup->getId(), $request->request->get('_token'))) {
            $facebookGroupsRepository->remove($facebookGroup, true);
        }

        return $this->redirectToRoute('facebook_groups_index', [], Response::HTTP_SEE_OTHER);
    }
}

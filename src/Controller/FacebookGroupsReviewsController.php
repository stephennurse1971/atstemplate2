<?php

namespace App\Controller;

use App\Entity\FacebookGroupsReviews;
use App\Form\FacebookGroupsReviewsType;
use App\Repository\FacebookGroupsRepository;
use App\Repository\FacebookGroupsReviewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/facebook_group_reviews")
 */
class FacebookGroupsReviewsController extends AbstractController
{
    /**
     * @Route("/", name="facebook_groups_reviews_index", methods={"GET"})
     */
    public function index(FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository): Response
    {
        return $this->render('facebook_groups_reviews/index.html.twig', [
            'facebook_groups_reviews' => $facebookGroupsReviewsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{facebookGroupId}", name="facebook_groups_reviews_new", methods={"GET", "POST"}, defaults={"facebookGroupId"="NULL"})
     */
    public function new(Request $request, $facebookGroupId,FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository,FacebookGroupsRepository $facebookGroupsRepository): Response
    {
        $facebookGroupName = '';
        $facebookGroupsReview = new FacebookGroupsReviews();
        $facebookGroup = $facebookGroupsRepository->findAll();
        $form = $this->createForm(FacebookGroupsReviewsType::class, $facebookGroupsReview);
        if($facebookGroupId > 0 ) {
            $facebookGroup = $facebookGroupsRepository->findBy(['id'=>$facebookGroupId]);
            $facebookGroupName = $facebookGroupsRepository->find($facebookGroupId)->getName();
        }
        $form = $this->createForm(FacebookGroupsReviewsType::class, $facebookGroupsReview, ['facebookGroup' => $facebookGroup,'mode'=>'new']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facebookGroupsReviewsRepository->add($facebookGroupsReview, true);
            return $this->redirectToRoute('facebook_groups_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facebook_groups_reviews/new.html.twig', [
            'facebook_groups_review' => $facebookGroupsReview,
            'form' => $form,
            'facebookGroupName' =>$facebookGroupName,
        ]);
    }


    /**
     * @Route("/new_nothing_of_note/{facebookGroupId}", name="facebook_groups_reviews_new_nothing_of_note", methods={"GET", "POST"}, defaults={"facebookGroupId"="NULL"})
     */
    public function newNothingOfNote(Request $request, $facebookGroupId,FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository,FacebookGroupsRepository $facebookGroupsRepository, Security $security): Response
    {
        $user = $security->getUser();
        $facebookGroupsReview = new FacebookGroupsReviews();
        $now = new \DateTime('now');
        if($facebookGroupId > 0 ) {
             $facebookGroupsReview->setFacebookGroup($facebookGroupsRepository->find($facebookGroupId));
             $facebookGroupsReview->setDate($now);
             $facebookGroupsReview->setComment('Nothing of note');
             $facebookGroupsReview->setReviewer($user);
        }
            $facebookGroupsReviewsRepository->add($facebookGroupsReview, true);
            return $this->redirectToRoute('facebook_groups_index', [], Response::HTTP_SEE_OTHER);

    }

    /**
     * @Route("/{id}", name="facebook_groups_reviews_show", methods={"GET"})
     */
    public function show(FacebookGroupsReviews $facebookGroupsReview): Response
    {
        return $this->render('facebook_groups_reviews/show.html.twig', [
            'facebook_groups_review' => $facebookGroupsReview,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="facebook_groups_reviews_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, FacebookGroupsReviews $facebookGroupsReview, FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository): Response
    {
        $form = $this->createForm(FacebookGroupsReviewsType::class, $facebookGroupsReview);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facebookGroupsReviewsRepository->add($facebookGroupsReview, true);
            return $this->redirectToRoute('facebook_groups_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('facebook_groups_reviews/edit.html.twig', [
            'facebook_groups_review' => $facebookGroupsReview,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="facebook_groups_reviews_delete", methods={"POST"})
     */
    public function delete(Request $request, FacebookGroupsReviews $facebookGroupsReview, FacebookGroupsReviewsRepository $facebookGroupsReviewsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $facebookGroupsReview->getId(), $request->request->get('_token'))) {
            $facebookGroupsReviewsRepository->remove($facebookGroupsReview, true);
        }

        return $this->redirectToRoute('facebook_groups_reviews_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\Referrals;
use App\Form\ReferralsType;
use App\Repository\BusinessContactsRepository;
use App\Repository\BusinessTypesRepository;
use App\Repository\ReferralsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/referrals")
 */
class ReferralsController extends AbstractController
{
    /**
     * @Route("/", name="referrals_index", methods={"GET"})
     */
    public function index(ReferralsRepository $referralsRepository): Response
    {
        return $this->render('referrals/index.html.twig', [
            'referrals' => $referralsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="referrals_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ReferralsRepository $referralsRepository): Response
    {
        $referral = new Referrals();
        $form = $this->createForm(ReferralsType::class, $referral);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $referralsRepository->add($referral, true);

            return $this->redirectToRoute('referrals_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('referrals/new.html.twig', [
            'referral' => $referral,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new_from_businesscontacts/{business_referred}/{action}", name="referrals_new_from_business_contact", methods={"GET", "POST"})
     */
    public function newFromBusinessContacts(Request $request, int $business_referred, string $action, ReferralsRepository $referralsRepository, BusinessTypesRepository $businessTypesRepository, BusinessContactsRepository $businessContactsRepository,EntityManagerInterface $manager,Security $security): Response
    {
        $user = $security->getUser();
        $now = new \DateTime('now');
        $referral = new Referrals();
        $referral->setBusinessSite($businessContactsRepository->find($business_referred))
            ->setAction($action)
            ->setDateTime($now);
        if($user){
            $referral->setUser($user);
        }
        $manager->persist($referral);
        $manager->flush();
        return $this->redirectToRoute('business_contacts_index');
    }

    /**
     * @Route("/show/{id}", name="referrals_show", methods={"GET"})
     */
    public function show(Referrals $referral): Response
    {
        return $this->render('referrals/show.html.twig', [
            'referral' => $referral,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="referrals_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Referrals $referral, ReferralsRepository $referralsRepository): Response
    {
        $form = $this->createForm(ReferralsType::class, $referral);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $referralsRepository->add($referral, true);

            return $this->redirectToRoute('referrals_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('referrals/edit.html.twig', [
            'referral' => $referral,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="referrals_delete", methods={"POST"})
     */
    public function delete(Request $request, Referrals $referral, ReferralsRepository $referralsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $referral->getId(), $request->request->get('_token'))) {
            $referralsRepository->remove($referral, true);
        }

        return $this->redirectToRoute('referrals_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/referrals/delete_all", name="referrals_delete_all")
     */
    public function deleteAllReferrals(ReferralsRepository $referralsRepository,EntityManagerInterface $entityManager): Response
    {
        $referrals = $referralsRepository->findAll();
        foreach ($referrals as $referral) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($referral);
            $entityManager->flush();
        }
        return $this->redirectToRoute('referrals_index');
    }



}

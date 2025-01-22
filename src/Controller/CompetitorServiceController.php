<?php

namespace App\Controller;

use App\Entity\CompetitorService;
use App\Form\CompetitorServiceType;
use App\Repository\CompetitorServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/competitor_service")
 */
class CompetitorServiceController extends AbstractController
{
    /**
     * @Route("/index", name="competitor_service_index", methods={"GET"})
     */
    public function index(CompetitorServiceRepository $competitorServiceRepository): Response
    {
        return $this->render('competitor_service/index.html.twig', [
            'competitor_services' => $competitorServiceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="competitor_service_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CompetitorServiceRepository $competitorServiceRepository): Response
    {
        $competitorService = new CompetitorService();
        $form = $this->createForm(CompetitorServiceType::class, $competitorService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competitorServiceRepository->add($competitorService, true);

            return $this->redirectToRoute('competitor_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('competitor_service/new.html.twig', [
            'competitor_service' => $competitorService,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/show/{id}", name="competitor_service_show", methods={"GET"})
     */
    public function show(CompetitorService $competitorService): Response
    {
        return $this->render('competitor_service/show.html.twig', [
            'competitor_service' => $competitorService,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="competitor_service_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CompetitorService $competitorService, CompetitorServiceRepository $competitorServiceRepository): Response
    {
        $form = $this->createForm(CompetitorServiceType::class, $competitorService);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $competitorServiceRepository->add($competitorService, true);

            return $this->redirectToRoute('competitor_service_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('competitor_service/edit.html.twig', [
            'competitor_service' => $competitorService,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="competitor_service_delete", methods={"POST"})
     */
    public function delete(Request $request, CompetitorService $competitorService, CompetitorServiceRepository $competitorServiceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$competitorService->getId(), $request->request->get('_token'))) {
            $competitorServiceRepository->remove($competitorService, true);
        }
        return $this->redirectToRoute('competitor_service_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\EmailsImport;
use App\Form\EmailsImportType;
use App\Repository\EmailsImportRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/emails/import")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class EmailsImportController extends AbstractController
{
    /**
     * @Route("/index", name="emails_import_index", methods={"GET"})
     */
    public function index(EmailsImportRepository $emailsImportRepository): Response
    {
        return $this->render('emails_import/index.html.twig', [
            'emails_imports' => $emailsImportRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="emails_import_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EmailsImportRepository $emailsImportRepository): Response
    {
        $emailsImport = new EmailsImport();
        $form = $this->createForm(EmailsImportType::class, $emailsImport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emailsImportRepository->add($emailsImport);
            return $this->redirectToRoute('emails_import_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('emails_import/new.html.twig', [
            'emails_import' => $emailsImport,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="emails_import_show", methods={"GET"})
     */
    public function show(EmailsImport $emailsImport): Response
    {
        return $this->render('emails_import/show.html.twig', [
            'emails_import' => $emailsImport,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="emails_import_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EmailsImport $emailsImport, EmailsImportRepository $emailsImportRepository): Response
    {
        $form = $this->createForm(EmailsImportType::class, $emailsImport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emailsImportRepository->add($emailsImport);
            return $this->redirectToRoute('emails_import_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('emails_import/edit.html.twig', [
            'emails_import' => $emailsImport,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="emails_import_delete", methods={"POST"})
     */
    public function delete(Request $request, EmailsImport $emailsImport, EmailsImportRepository $emailsImportRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$emailsImport->getId(), $request->request->get('_token'))) {
            $emailsImportRepository->remove($emailsImport);
        }

        return $this->redirectToRoute('emails_import_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\EmailsImport;
use App\Form\EmailsImportType;
use App\Repository\EmailsImportRepository;
use App\Services\EmailFetcherService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

/**
 * @Route("/emails_import")
 * @Security("is_granted('ROLE_ADMIN')")
 */


class EmailsImportController extends AbstractController
{
    private $emailFetcher;

    public function __construct(EmailFetcherService $emailFetcher)
    {
        $this->emailFetcher = $emailFetcher;
    }

    /**
     * @Route("/index", name="emails_import_index", methods={"GET"})
     */
    public function index(EmailsImportRepository $emailsImportRepository): Response
    {

        return $this->render('emails_import/index.html.twig', [
            'emails' => $emailsImportRepository->findAll(),
        ]);
    }


    /**
     * @Route("/emails_fetch", name="emails_fetch")
     */
    public function fetchEmails(LoggerInterface $logger): Response
    {
        try {
            // Use the injected EmailFetcherService to fetch emails
            $emails = $this->emailFetcher->fetchEmails();

            if (isset($emails['error'])) {
                // If an error occurred while fetching, log and return the error message
                $logger->warning('Failed to fetch emails: ' . $emails['error']);
                return new Response(
                    'An error occurred while fetching emails: ' . $emails['error'],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            // If emails were fetched successfully, pass them to the Twig template
            return $this->render('emails_import/index.html.twig', [
                'emails_imports' => $emails,
            ]);
        } catch (\Exception $e) {
            // Log the exception
            $logger->error('Failed to fetch emails: ' . $e->getMessage());

            // Return a simple error message in the response
            return new Response(
                'An error occurred while fetching emails: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
//        $emails = $this->emailFetcher->fetchEmails();
//        dump($emails);exit();
//
//        return $this->render('emails_import/index.html.twig', [
//            'emails' => $emails,
//        ]);
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
            'emails_imports' => $emailsImport,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="emails_import_show", methods={"GET"})
     */
    public function show(EmailsImport $emailsImport): Response
    {
        return $this->render('emails_import/show.html.twig', [
            'emails_imports' => $emailsImport,
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
            'emails_imports' => $emailsImport,
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

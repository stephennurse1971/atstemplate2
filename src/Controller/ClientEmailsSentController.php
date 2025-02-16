<?php

namespace App\Controller;

use App\Entity\ClientEmailsSent;
use App\Entity\User;
use App\Form\ClientEmailsSentType;
use App\Repository\ClientEmailsSentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/client_emails_sent')]
final class ClientEmailsSentController extends AbstractController
{
    #[Route(name: 'client_emails_sent_index', methods: ['GET'])]
    public function index(ClientEmailsSentRepository $clientEmailsSentRepository): Response
    {
        return $this->render('client_emails_sent/index.html.twig', [
            'client_emails_sents' => $clientEmailsSentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'client_emails_sent_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Initialize ClientEmailsSent entity
        $clientEmailsSent = new ClientEmailsSent();
        $form = $this->createForm(ClientEmailsSentType::class, $clientEmailsSent);
        $form->handleRequest($request);

        // Handle form submission
        if ($form->isSubmitted() && $form->isValid()) {
            // If you need to add a user (e.g., current logged-in user)
            $user = $this->getUser(); // Get the currently logged-in user from the security service
            if ($user) {
                $clientEmailsSent->addRecipient($user);
            }

            // Persist the recipient and the ClientEmailsSent entity
            foreach ($clientEmailsSent->getRecipient() as $recipient) {
                $entityManager->persist($recipient);
            }

            $entityManager->persist($clientEmailsSent);
            $entityManager->flush();

            return $this->redirectToRoute('client_emails_sent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client_emails_sent/new.html.twig', [
            'client_emails_sent' => $clientEmailsSent,
            'form' => $form,
        ]);
    }


    #[Route('/show/{id}', name: 'client_emails_sent_show', methods: ['GET'])]
    public function show(ClientEmailsSent $clientEmailsSent): Response
    {
        return $this->render('client_emails_sent/show.html.twig', [
            'client_emails_sent' => $clientEmailsSent,
        ]);
    }

    #[Route('/edit/{id}', name: 'client_emails_sent_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ClientEmailsSent $clientEmailsSent, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientEmailsSentType::class, $clientEmailsSent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_client_emails_sent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client_emails_sent/edit.html.twig', [
            'client_emails_sent' => $clientEmailsSent,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'client_emails_sent_delete', methods: ['POST'])]
    public function delete(Request $request, ClientEmailsSent $clientEmailsSent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$clientEmailsSent->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($clientEmailsSent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('client_emails_sent_index', [], Response::HTTP_SEE_OTHER);
    }
}

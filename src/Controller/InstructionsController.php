<?php

namespace App\Controller;

use App\Entity\Instructions;
use App\Form\InstructionsType;
use App\Repository\InstructionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/instructions')]
class InstructionsController extends AbstractController
{
    #[Route('/index', name: 'instructions_index', methods: ['GET'])]
    public function index(InstructionsRepository $instructionsRepository): Response
    {
        return $this->render('instructions/index.html.twig', [
            'instructions' => $instructionsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'instructions_new', methods: ['GET', 'POST'])]
    public function new(Request $request, InstructionsRepository $instructionsRepository): Response
    {
        $instruction = new Instructions();
        $form = $this->createForm(InstructionsType::class, $instruction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $media = $form->get('media')->getData();
            $originalFilename = pathinfo($media->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename .".". $media->guessExtension();
            try {
                $media->move(
                    $this->getParameter('instructions_attachments_directory'),
                    $newFilename
                );
                $instruction->setMedia($newFilename);
            } catch (FileException $e) {
                die('Import failed');
            }
           $instructionsRepository->add($instruction, true);

            return $this->redirectToRoute('instructions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('instructions/new.html.twig', [
            'instruction' => $instruction,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{topic}', name: 'instructions_show', methods: ['GET'])]
    public function show(Request $request, string $topic, InstructionsRepository $instructionsRepository): Response
    {
        $instruction=$instructionsRepository->findOneBy(['topic'=>$topic]);
        return $this->render('instructions/show.html.twig', [
            'instruction' => $instruction,
        ]);
    }

    #[Route('/edit/{id}', name: 'instructions_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Instructions $instruction, InstructionsRepository $instructionsRepository): Response
    {
        $form = $this->createForm(InstructionsType::class, $instruction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $media = $form->get('media')->getData();
            if($media) {
                $originalFilename = pathinfo($media->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename .".". $media->guessExtension();
                try {
                    $media->move(
                        $this->getParameter('instructions_attachments_directory'),
                        $newFilename
                    );
                    $instruction->setMedia($newFilename);
                } catch (FileException $e) {
                    die('Import failed');
                }
            }
            $instructionsRepository->add($instruction, true);

            return $this->redirectToRoute('instructions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('instructions/edit.html.twig', [
            'instruction' => $instruction,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'instructions_delete', methods: ['POST'])]
    public function delete(Request $request, Instructions $instruction, InstructionsRepository $instructionsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $instruction->getId(), $request->request->get('_token'))) {
            $instructionsRepository->remove($instruction, true);
        }

        return $this->redirectToRoute('instructions_index', [], Response::HTTP_SEE_OTHER);
    }
}

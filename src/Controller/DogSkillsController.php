<?php

namespace App\Controller;

use App\Entity\DogSkills;
use App\Form\DogSkillsType;
use App\Repository\DogSkillsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dog/skills')]
class DogSkillsController extends AbstractController
{
    #[Route('/', name: 'app_dog_skills_index', methods: ['GET'])]
    public function index(DogSkillsRepository $dogSkillsRepository): Response
    {
        return $this->render('dog_skills/index.html.twig', [
            'dog_skills' => $dogSkillsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dog_skills_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DogSkillsRepository $dogSkillsRepository): Response
    {
        $dogSkill = new DogSkills();
        $form = $this->createForm(DogSkillsType::class, $dogSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dogSkillsRepository->add($dogSkill, true);

            return $this->redirectToRoute('app_dog_skills_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dog_skills/new.html.twig', [
            'dog_skill' => $dogSkill,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dog_skills_show', methods: ['GET'])]
    public function show(DogSkills $dogSkill): Response
    {
        return $this->render('dog_skills/show.html.twig', [
            'dog_skill' => $dogSkill,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dog_skills_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DogSkills $dogSkill, DogSkillsRepository $dogSkillsRepository): Response
    {
        $form = $this->createForm(DogSkillsType::class, $dogSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dogSkillsRepository->add($dogSkill, true);

            return $this->redirectToRoute('app_dog_skills_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('dog_skills/edit.html.twig', [
            'dog_skill' => $dogSkill,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_dog_skills_delete', methods: ['POST'])]
    public function delete(Request $request, DogSkills $dogSkill, DogSkillsRepository $dogSkillsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dogSkill->getId(), $request->request->get('_token'))) {
            $dogSkillsRepository->remove($dogSkill, true);
        }

        return $this->redirectToRoute('app_dog_skills_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Form\ImportType;
use App\Repository\UserRepository;
use App\Services\ImportUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * @Route("/admin")
 */
class ImportController extends AbstractController
{
    /**
     * @Route("/user_import", name="user_import")
     */
    public function userImport(Request $request, SluggerInterface $slugger, UserRepository $userRepository, ImportUserService $importUserService): Response
    {
        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $importFile = $form->get('File')->getData();
            if ($importFile) {
                $originalFilename = pathinfo($importFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '.' . 'csv';
                try {
                    $importFile->move(
                        $this->getParameter('user_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }

                $importUserService->importUser($newFilename);
                return $this->redirectToRoute('user_index');
            }
        }
        return $this->render('admin/import/index.html.twig', [
            'form' => $form->createView(),
            'heading' => 'All'
        ]);

        return $this->redirectToRoute('user_index');
    }
}

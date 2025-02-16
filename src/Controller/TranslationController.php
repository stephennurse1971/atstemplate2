<?php

namespace App\Controller;

use App\Entity\Translation;
use App\Form\ImportType;
use App\Form\TranslationType;
use App\Repository\TranslationRepository;
use App\Services\TranslationsImportService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/translation")
 * @Security("is_granted('ROLE_ADMIN')")
 */

class TranslationController extends AbstractController
{
    #[Route('/index', name: 'translation_index', methods: ['GET'])]
    public function index(TranslationRepository $translationRepository): Response
    {
        return $this->render('translation/index.html.twig', [
            'translations' => $translationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'translation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TranslationRepository $translationRepository): Response
    {
        $translation = new Translation();
        $form = $this->createForm(TranslationType::class, $translation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $translationRepository->add($translation, true);

            return $this->redirectToRoute('translation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('translation/new.html.twig', [
            'translation' => $translation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'translation_show', methods: ['GET'])]
    public function show(Translation $translation): Response
    {
        return $this->render('translation/show.html.twig', [
            'translation' => $translation,
        ]);
    }

    #[Route('/edit/{id}', name: 'translation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Translation $translation, TranslationRepository $translationRepository): Response
    {
        $form = $this->createForm(TranslationType::class, $translation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $translationRepository->add($translation, true);

            return $this->redirectToRoute('translation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('translation/edit.html.twig', [
            'translation' => $translation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'translation_delete', methods: ['POST'])]
    public function delete(Request $request, Translation $translation, TranslationRepository $translationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $translation->getId(), $request->request->get('_token'))) {
            $translationRepository->remove($translation, true);
        }

        return $this->redirectToRoute('translation_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete_all", name="translation_delete_all")
     */
    public function deleteAllTranslations(TranslationRepository $translationRepository, EntityManagerInterface $entityManager): Response
    {
        $translations = $translationRepository->findAll();
        foreach ($translations as $translation) {
            $entityManager->remove($translation);
            $entityManager->flush();
        }
        return $this->redirectToRoute('translation_index');
    }

    #[Route('/export', name: 'translation_export', methods: ['GET'])]
    public function exportTranslations(TranslationRepository $translationRepository): Response
    {
        $data = [];
        $fileName = 'translations_export.csv';
        $translations = $translationRepository->findAll();
        foreach ($translations as $translation) {
            $data[] = [
                "Translations",
                $translation->getEntity(),
                $translation->getEnglish(),
                $translation->getFrench(),
                $translation->getGerman(),
                $translation->getSpanish(),
                $translation->getRussian(),
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Translations');
        $sheet->getCell('A1')->setValue('Where Used');
        $sheet->getCell('B1')->setValue('Entity');
        $sheet->getCell('C1')->setValue('English');
        $sheet->getCell('D1')->setValue('French');
        $sheet->getCell('E1')->setValue('German');
        $sheet->getCell('F1')->setValue('Spanish');
        $sheet->getCell('G1')->setValue('Russian');

        $sheet->fromArray($data, null, 'A2', true);
        $total_rows = $sheet->getHighestRow();
        for ($i = 2; $i <= $total_rows; $i++) {
            $cell = "L" . $i;
            $sheet->getCell($cell)->getHyperlink()->setUrl("https://google.com");
        }
        $writer = new Csv($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', sprintf('attachment;filename="%s"', $fileName));
        $response->headers->set('Cache-Control', 'max-age=0');
        return $response;
    }


    /**
     * @Route("/import", name="translation_import")
     */
    public function translationImport(Request $request,SluggerInterface $slugger, TranslationRepository $translationRepository, TranslationsImportService $translationsImportService): Response
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
                        $this->getParameter('translations_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $translationsImportService->importTranslations($newFilename);
                return $this->redirectToRoute('translation_index');

            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading'=>'Translations',
        ]);
        return $this->redirectToRoute('translation_index');
    }


}

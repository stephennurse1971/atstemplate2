<?php

namespace App\Controller;

use App\Entity\Languages;
use App\Form\ImportType;
use App\Form\LanguagesType;
use App\Repository\BusinessTypesRepository;
use App\Repository\LanguagesRepository;
use App\Services\BusinessTypesImportService;
use App\Services\LanguagesImportService;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/languages')]
class LanguagesController extends AbstractController
{
    #[Route('/index', name: 'languages_index', methods: ['GET'])]
    public function index(LanguagesRepository $languagesRepository): Response
    {
        return $this->render('languages/index.html.twig', [
            'languages' => $languagesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'languages_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LanguagesRepository $languagesRepository): Response
    {
        $language = new Languages();
        $form = $this->createForm(LanguagesType::class, $language);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->isSubmitted() && $form->isValid()) {
                $languagesRepository->add($language, true);
                $icon = $form->get('icon')->getData();
                if ($icon) {
                    $safeFilename = $language->getAbbreviation();
                    $newFilename = $safeFilename . '.' . $icon->guessExtension();
                    try {
                        $icon->move(
                            $this->getParameter('icon_directory'),
                            $newFilename
                        );
                        $language->setIcon($newFilename);
                    } catch (FileException $e) {
                        die('Import failed');
                    }
                }
            }
            $languagesRepository->add($language, true);

            return $this->redirectToRoute('languages_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('languages/new.html.twig', [
            'language' => $language,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'languages_show', methods: ['GET'])]
    public function show(Languages $language): Response
    {
        return $this->render('languages/show.html.twig', [
            'language' => $language,
        ]);
    }

    #[Route('/edit/{id}', name: 'languages_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Languages $language, LanguagesRepository $languagesRepository): Response
    {
        $form = $this->createForm(LanguagesType::class, $language);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->isSubmitted() && $form->isValid()) {
                $languagesRepository->add($language, true);
                $icon = $form->get('icon')->getData();
                if ($icon) {
                    $safeFilename = $language->getAbbreviation();
                    $newFilename = $safeFilename . '.' . $icon->guessExtension();
                    try {
                        $icon->move(
                            $this->getParameter('icon_directory'),
                            $newFilename
                        );
                        $language->setIcon($newFilename);
                    } catch (FileException $e) {
                        die('Import failed');
                    }
                }
            }
            $languagesRepository->add($language, true);

            return $this->redirectToRoute('languages_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('languages/edit.html.twig', [
            'language' => $language,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'languages_delete', methods: ['POST'])]
    public function delete(Request $request, Languages $language, LanguagesRepository $languagesRepository, EntityManagerInterface $entityManager): Response
    {
        $file_name = $language->getIcon();
        if ($file_name) {
            $file = $this->getParameter('icon_directory') . $file_name;
            if (file_exists($file)) {
                unlink($file);
            }
            $language->setIcon('');
            $entityManager->flush();

        }
        if ($this->isCsrfTokenValid('delete' . $language->getId(), $request->request->get('_token'))) {
            $languagesRepository->remove($language, true);
        }

        return $this->redirectToRoute('languages_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/delete_all", name="languages_delete_all")
     */
    public function deleteLanguages(LanguagesRepository $languagesRepository, EntityManagerInterface $entityManager): Response
    {
        $languages = $languagesRepository->findAll();
        foreach ($languages as $language) {
            $entityManager->remove($language);
            $entityManager->flush();
        }
        return $this->redirectToRoute('languages_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete_language_icon/{id}", name="language_icon_delete", methods={"POST", "GET"})
     */
    public function deleteLanguageIcon(int $id, Request $request, Languages $language, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        $file_name = $language->getIcon();
        if ($file_name) {
            $file = $this->getParameter('icon_directory') . $file_name;
            if (file_exists($file)) {
                unlink($file);
            }
            $language->setIcon('');
            $entityManager->flush();

        }
        return $this->redirect($referer);
    }

    /**
     * @Route("/set/default/language/{id}", name="select_default_language")
     */
    public function setDefaultLanguage(Request $request, Languages $languages, Security $security, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $user = $security->getUser();
        if($user){
            $user->setDefaultLanguage($languages);
            $entityManager->persist($user);
            $entityManager->flush();
        }
        $session->set('selected_language', $languages->getLanguage());
        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * @Route("/change_ranking/{change}/{id}", name="language_change_ranking", methods={"GET", "POST"})
     */
    public
    function changePriority(Request $request, $change, Languages $languages, EntityManagerInterface $manager): Response
    {
        $referer = $request->headers->get('Referer');
        $currentRanking = $languages->getRanking();
        if ($change == "Up") {
            $newRanking = $currentRanking - 1;
        }
        if ($change == "Down") {
            $newRanking = $currentRanking + 1;
        }
        $languages->setRanking($newRanking);
        $manager->flush();
        return $this->redirect($referer);
    }


    /**
     * @Route("/update_ranking", name="update_ranking", methods={"GET", "POST"})
     */
    public function updateLanguageRanking(LanguagesRepository $languagesRepository, Request $request, EntityManagerInterface $manager): Response
    {
        $referer = $request->headers->get('Referer');
        $rankingContainer = [];
        $languages = $languagesRepository->findAll();
        foreach ($languages as $language) {
            $rankingContainer[] = ['id' => $language->getId(), 'ranking' => $language->getRanking()];
            array_multisort(array_column($rankingContainer, 'ranking'), SORT_ASC, $rankingContainer);
            $minRank = 1;

            foreach ($rankingContainer as $sortedItem) {
                $language = $languagesRepository->find($sortedItem['id']);
                $language->setRanking($minRank);
                $manager->flush();
                $minRank = $minRank + 1;
            }
        }
        return $this->redirect($referer);
    }

    /**
     * @Route("/language_change_active_or_inactive/{id}", name="language_change_active_or_inactive", methods={"GET", "POST"})
     */
    public function changeActiveOrInactive(Request $request, string $id, LanguagesRepository $languagesRepository, EntityManagerInterface $manager): Response
    {
        $referer = $request->headers->get('Referer');
        $language = $languagesRepository->find($id);
        $current_status = $language->isIsActive();

        if ($current_status == 1) {
            $language->setIsActive(0);
            $manager->persist($language);
            $manager->flush();
        }
        if ($current_status == 0) {
            $language->setIsActive(1);
            $manager->persist($language);
            $manager->flush();
        }

        return $this->redirect($referer);
    }


    /**
     * @Route ("/export/languages", name="languages_export" )
     */
    public function languagesExport(LanguagesRepository $languagesRepository)
    {
        $data = [];
        $exported_date = new \DateTime('now');
        $exported_date_formatted = $exported_date->format('d-M-Y');
        $exported_date_formatted_for_file = $exported_date->format('d-m-Y');
        $fileName = 'languages_export_' . $exported_date_formatted_for_file . '.csv';

        $count = 0;
        $languages_list = $languagesRepository->findAll();
        foreach ($languages_list as $language) {
            $data[] = [
                $language->getRanking(),
                $language->isIsActive(),
                $language->getLanguage(),
                $language->getAbbreviation(),
                $language->getLinkedInOther(),
                $language->getIcon(),
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Languages');
        $sheet->getCell('A1')->setValue('Ranking');
        $sheet->getCell('B1')->setValue('IsActive');
        $sheet->getCell('C1')->setValue('Language');
        $sheet->getCell('D1')->setValue('Abbreviation');
        $sheet->getCell('E1')->setValue('LinkedIn Other');
        $sheet->getCell('F1')->setValue('Icon');

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
     * @Route ("/import/languages", name="languages_import" )
     */
    public function languagesImport(Request $request, SluggerInterface $slugger, LanguagesRepository $languagesRepository, LanguagesImportService $languagesImportService): Response
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
                        $this->getParameter('languages_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $languagesImportService->importLanguages($newFilename);
                return $this->redirectToRoute('languages_index');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'Languages Import',
        ]);
    }
}

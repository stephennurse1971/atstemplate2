<?php

namespace App\Controller;

use App\Entity\CompanyDetails;
use App\Form\CompanyDetailsType;
use App\Form\ImportType;
use App\Repository\CompanyDetailsRepository;
use App\Services\ImportCompanyDetailsService;
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
 * @Route("/company_details")
 */
class CompanyDetailsController extends AbstractController
{
    /**
     * @Route("/index", name="company_details_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(CompanyDetailsRepository $companyDetailsRepository): Response
    {
        return $this->render('company_details/index.html.twig', [
            'company_details' => $companyDetailsRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="company_details_new", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request, CompanyDetailsRepository $companyDetailsRepository): Response
    {
        $companyDetails = new CompanyDetails();
        $form = $this->createForm(CompanyDetailsType::class, $companyDetails);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $faviconDev = $form['faviconDev']->getData();
            $faviconLive = $form['faviconLive']->getData();
            $qrCode = $form['companyQrCode']->getData();

            if ($faviconDev) {
                $originalFilename = pathinfo($faviconDev->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $companyDetails->getCompanyName() . '_dev.' . $faviconDev->guessExtension();
                $faviconDev->move(
                    $this->getParameter('favicons_directory'),
                    $newFilename
                );
                $companyDetails->setFaviconDev($newFilename);
            }
            if ($faviconLive) {
                $originalFilenameLive = pathinfo($faviconLive->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilenameLive = $companyDetails->getCompanyName() . '_live.' . $faviconLive->guessExtension();
                $faviconLive->move(
                    $this->getParameter('favicons_directory'),
                    $newFilenameLive
                );
                $companyDetails->setFaviconLive($newFilenameLive);
            }
            if ($qrCode) {
                $originalFilenameQR = pathinfo($qrCode->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilenameQR = $companyDetails->getCompanyName() . '_qr_code.' . $qrCode->guessExtension();
                $qrCode->move(
                    $this->getParameter('favicons_directory'),
                    $newFilenameQR
                );
                $companyDetails->setCompanyQrCode($newFilenameQR);
            }
            $companyDetailsRepository->add($companyDetails, true);

            return $this->redirectToRoute('company_details_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company_details/new.html.twig', [
            'company_details' => $companyDetails,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="company_details_show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(CompanyDetails $companyDetails): Response
    {
        return $this->render('company_details/show.html.twig', [
            'company_detail' => $companyDetails,
            'hide_for_qr_code' => '0'
        ]);
    }

    /**
     * @Route("/show_qr_build/{id}", name="company_details_show_qr_build", methods={"GET"})
     *
     */
    public function showQRBuild(CompanyDetails $companyDetails): Response
    {
        return $this->render('company_details/show_qr_build.html.twig', [
            'company_detail' => $companyDetails,
            'hide_for_qr_code' => '1'
        ]);
    }

    /**
     * @Route("/edit/{id}", name="company_details_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, CompanyDetails $companyDetails, CompanyDetailsRepository $companyDetailsRepository): Response
    {

        $form = $this->createForm(CompanyDetailsType::class, $companyDetails);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $faviconDev = $form['faviconDev']->getData();
            $faviconLive = $form['faviconLive']->getData();
            $qrCode = $form['companyQrCode']->getData();

            if ($faviconDev) {
                $originalFilename = pathinfo($faviconDev->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $companyDetails->getCompanyName() . '_dev.' . $faviconDev->guessExtension();
                $faviconDev->move(
                    $this->getParameter('favicons_directory'),
                    $newFilename
                );
                $companyDetails->setFaviconDev($newFilename);
            }
            if ($faviconLive) {
                $originalFilenameLive = pathinfo($faviconLive->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilenameLive = $companyDetails->getCompanyName() . '_live.' . $faviconLive->guessExtension();
                $faviconLive->move(
                    $this->getParameter('favicons_directory'),
                    $newFilenameLive
                );
                $companyDetails->setFaviconLive($newFilenameLive);
            }
            if ($qrCode) {
                $originalFilenameQR = pathinfo($qrCode->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilenameQR = $companyDetails->getCompanyName() . '_qr_code.' . $qrCode->guessExtension();
                $qrCode->move(
                    $this->getParameter('favicons_directory'),
                    $newFilenameQR
                );
                $companyDetails->setCompanyQrCode($newFilenameQR);
            }

            $companyDetailsRepository->add($companyDetails, true);

            return $this->redirectToRoute('company_details_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company_details/edit.html.twig', [
            'company_details' => $companyDetails,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="company_details_delete", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, CompanyDetails $companyDetails, CompanyDetailsRepository $companyDetailsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $companyDetails->getId(), $request->request->get('_token'))) {
            $companyDetailsRepository->remove($companyDetails, true);
        }

        return $this->redirectToRoute('company_details_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/office_address", name="office_address", methods={"GET"})
     */
    public function officeAddress(CompanyDetailsRepository $companyDetailsRepository): Response
    {
        return $this->render('home/officeAddress.html.twig');
    }

    /**
     * @Route("/delete_favicon/{live_or_dev}/{id}", name="company_details_delete_favicon", methods={"POST", "GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteLiveFavicon(Request $request, int $id, string $live_or_dev, CompanyDetails $companyDetails, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        if ($live_or_dev == 'live') {
            $companyDetails->setFaviconLive(null);
            $entityManager->flush();
            $files = glob($this->getParameter('favicons_directory') . "/*live*");
            foreach ($files as $file) {
                unlink($file);
            }
        }
        if ($live_or_dev == 'dev') {
            $companyDetails->setFaviconDev(null);
            $entityManager->flush();
            $files = glob($this->getParameter('favicons_directory') . "/*dev*");
            foreach ($files as $file) {
                unlink($file);
            }
        }
        $entityManager->flush();
        return $this->redirect($referer);
    }


    /**
     * @Route("/delete_qr_code/{id}", name="company_details_delete_qr_code", methods={"POST", "GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteQRCodeLiveFavicon(Request $request, int $id, CompanyDetails $companyDetails, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        $companyDetails->setCompanyQrCode(null);
        $entityManager->flush();
        $files = glob($this->getParameter('favicons_directory') . "/*qr*");
        foreach ($files as $file) {
            unlink($file);
        }
        $entityManager->flush();
        return $this->redirect($referer);
    }

    /**
     * @Route("/export/database", name="export_database", methods={"POST", "GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function exportDatabase(Request $request, EntityManagerInterface $entityManager, \App\Services\CompanyDetailsService $companyDetails)
    {
        $sqlDatabase = $companyDetails->getCompanyDetails()->getSqlDatabase() . '.sql';
        $sqlPassword = $companyDetails->getCompanyDetails()->getDatabasePassword();
        $publicPath = $this->getParameter('public');
        $filePath = $publicPath . '/' . $sqlDatabase;

        if ($_ENV['APP_SERVER'] == "local") {
            exec('mysqldump --user=root --password= --host=localhost ' . escapeshellarg($sqlDatabase) . ' > ' . escapeshellarg($filePath));
        } else {
            exec('mysqldump --user=stephen --password=' . escapeshellarg($sqlPassword) . ' --host=localhost ' . escapeshellarg($sqlDatabase) . ' > ' . escapeshellarg($filePath));
        }

        if (file_exists($filePath)) {
            return $this->file($filePath)->deleteFileAfterSend(true); // Symfony helper to download files
        }

        // If file doesn't exist, redirect back with an error message
        $this->addFlash('error', 'Failed to export the database.');
        $referer = $request->headers->get('Referer');
        return $this->redirect($referer ?? $this->generateUrl('app_home'));
    }

    /**
     * @Route("/edit/update/location", name="update_company_details_location", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function updateLocation(CompanyDetailsRepository $companyDetailsRepository, EntityManagerInterface $manager): Response
    {
        $id = $_POST['id'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $company_details = $companyDetailsRepository->find($id);
        $company_details->setCompanyAddressLongitude($longitude)
            ->setCompanyAddressLatitude($latitude);
        $manager->flush();
        return new Response(null);
    }


    /**
     * @Route("/company_details_change_field_status/{input}", name="company_details_change_field_status", methods={"GET", "POST"})
     */
    public function changeStatus(Request $request, string $input, CompanyDetailsRepository $companyDetailsRepository, EntityManagerInterface $manager): Response
    {
        $referer = $request->headers->get('Referer');
        $company_details = $companyDetailsRepository->find('1');

        $fieldname = $input;
        $getter = 'is' . ucfirst($fieldname);
        $setter = 'set' . ucfirst($fieldname);

        if (method_exists($company_details, $getter) && method_exists($company_details, $setter)) {
            $newValue = !$company_details->$getter();
            $company_details->$setter($newValue);
        }

        $manager->persist($company_details);
        $manager->flush();
        return $this->redirect($referer);
    }

    /**
     * @Route ("/export", name="company_details_export" )
     */
    public function companyDetailsExport(CompanyDetailsRepository $companyDetailsRepository)
    {
        $data = [];
        $exported_date = new \DateTime('now');
        $exported_date_formatted = $exported_date->format('d-M-Y');
        $fileName = 'company_details_export_' . $exported_date_formatted . '.csv';
        $company_details_list = $companyDetailsRepository->findAll();
        $count = 0;

        foreach ($company_details_list as $company_details) {
            $data[] = [
                'CompanyDetails',
                $company_details->getCompanyName(),
                $company_details->getCompanyWebsite(),
                $company_details->getContactFirstName(),
                $company_details->getContactLastName(),
                $company_details->getCompanyTel(),
                $company_details->getCompanyMobile(),
                $company_details->getCompanySkype(),

                $company_details->getFaviconLive(),
                $company_details->getFaviconDev(),
                $company_details->getCompanyQrCode(),

                $company_details->getCompanyEmail(),
                $company_details->getCompanyEmailPassword(),
                $company_details->getCompanyEmailImportDirectory(),
                $company_details->getCompanyEmailImportProcessedDirectory(),
                $company_details->getSqlDatabase(),
                $company_details->getDatabasePassword(),

                $company_details->getCompanyAddressStreet(),
                $company_details->getCompanyAddressTown(),
                $company_details->getCompanyAddressCity(),
                $company_details->getCompanyAddressPostalCode(),
                $company_details->getCompanyAddressCountry(),
                $company_details->getCompanyAddressMapLink(),
                $company_details->getCompanyAddressLongitude(),
                $company_details->getCompanyAddressLatitude(),
                $company_details->getCompanyAddressInstructions(),

                $company_details->getWeatherLocation(),
                $company_details->getCompanyTimeZone(),
                $company_details->getCurrency(),

                $company_details->getFacebook(),
                $company_details->getTwitter(),
                $company_details->getInstagram(),
                $company_details->getLinkedIn(),

                $company_details->isHomePagePhotosOnly(),
                $company_details->isIncludeContactFormHomePage(),
                $company_details->isIncludeQRCodeHomePage(),
                $company_details->isMultiLingual(),

                $company_details->getTitleProducts(),
                $company_details->getTitleSubProducts(),
                $company_details->getTitleUsefulLinks(),

                $company_details->isEnableUserRegistration(),
                $company_details->isWebsiteContactsEmailAlert(),
                $company_details->getWebsiteContactsAutoReply(),
                $company_details->getRegistrationEmail(),

                $company_details->isHeaderDisplayProducts(),
                $company_details->isHeaderDisplaySubProducts(),
                $company_details->isHeaderDisplayPhotos(),
                $company_details->isHeaderDisplayLogin(),
                $company_details->isHeaderDisplayContactDetails(),
                $company_details->getHeaderDisplayPricing(),
                $company_details->getHeaderDisplayInstructions(),
                $company_details->getHeaderDisplayTandCs(),
                $company_details->isHeaderDisplayBusinessContacts(),
                $company_details->isHeaderDisplayFacebookPages(),
                $company_details->getFacebookReviewsHistoryShowMonths(),
                $company_details->isHeaderDisplayCompetitors(),
                $company_details->isHeaderDisplayWeather(),

                $company_details->isFooterDisplayContactDetails(),
                $company_details->isFooterDisplayAddress(),
                $company_details->isFooterDisplayTelNumbers(),
                $company_details->isFooterDisplaySocialMedia(),
                $company_details->isFooterDisplayProducts(),
                $company_details->isFooterDisplaySubProducts(),

                $company_details->isUserIncludeHomeAddress(),
                $company_details->isUserIncludeBusinessAddress(),
                $company_details->isUserIncludePersonalDetails(),
                $company_details->isUserIncludeJobDetails(),
            ];
        }
        $company_details_list = $companyDetailsRepository->findAll();
        $concatenatedNotes = "Exported on: " . $exported_date_formatted;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Company Details');


        $sheet->getCell('A1')->setValue('CompanyDetails');
        $sheet->getCell('B1')->setValue('CompanyName');
        $sheet->getCell('C1')->setValue('CompanyWebsite');
        $sheet->getCell('D1')->setValue('ContactFirstName');
        $sheet->getCell('E1')->setValue('ContactLastName');
        $sheet->getCell('F1')->setValue('CompanyTel');
        $sheet->getCell('G1')->setValue('CompanyMobile');
        $sheet->getCell('H1')->setValue('CompanySkype');

        $sheet->getCell('I1')->setValue('FaviconLive');
        $sheet->getCell('J1')->setValue('FaviconDev');
        $sheet->getCell('K1')->setValue('CompanyQrCode');

        $sheet->getCell('L1')->setValue('CompanyEmail');
        $sheet->getCell('M1')->setValue('CompanyEmailPassword');
        $sheet->getCell('N1')->setValue('CompanyEmailImportDirectory');
        $sheet->getCell('O1')->setValue('CompanyEmailImportProcessedDirectory');
        $sheet->getCell('P1')->setValue('SqlDatabase');
        $sheet->getCell('Q1')->setValue('DatabasePassword');

        $sheet->getCell('R1')->setValue('CompanyAddressStreet');
        $sheet->getCell('S1')->setValue('CompanyAddressTown');
        $sheet->getCell('T1')->setValue('CompanyAddressCity');
        $sheet->getCell('U1')->setValue('CompanyAddressPostalCode');
        $sheet->getCell('V1')->setValue('CompanyAddressCountry');
        $sheet->getCell('W1')->setValue('CompanyAddressMapLink');
        $sheet->getCell('X1')->setValue('CompanyAddressLongitude');
        $sheet->getCell('Y1')->setValue('CompanyAddressLatitude');
        $sheet->getCell('Z1')->setValue('CompanyAddressInstructions');

        $sheet->getCell('AA1')->setValue('WeatherLocation');
        $sheet->getCell('AB1')->setValue('CompanyTimeZone');
        $sheet->getCell('AC1')->setValue('Currency');

        $sheet->getCell('AD1')->setValue('Facebook');
        $sheet->getCell('AE1')->setValue('Twitter');
        $sheet->getCell('AF1')->setValue('Instagram');
        $sheet->getCell('AG1')->setValue('LinkedIn');

        $sheet->getCell('AH1')->setValue('HomePagePhotosOnly');
        $sheet->getCell('AI1')->setValue('IncludeContactFormHomePage');
        $sheet->getCell('AJ1')->setValue('IncludeQRCodeHomePage');
        $sheet->getCell('AK1')->setValue('MultiLingual');

        $sheet->getCell('AL1')->setValue('TitleProducts');
        $sheet->getCell('AM1')->setValue('TitleSubProducts');
        $sheet->getCell('AN1')->setValue('TitleUsefulLinks');

        $sheet->getCell('AO1')->setValue('EnableUserRegistration');
        $sheet->getCell('AP1')->setValue('WebsiteContactsEmailAlert');
        $sheet->getCell('AQ1')->setValue('WebsiteContactsAutoReply');
        $sheet->getCell('AR1')->setValue('RegistrationEmail');

        $sheet->getCell('AS1')->setValue('HeaderDisplayProducts');
        $sheet->getCell('AT1')->setValue('HeaderDisplaySubProducts');
        $sheet->getCell('AU1')->setValue('HeaderDisplayPhotos');
        $sheet->getCell('AV1')->setValue('HeaderDisplayLogin');
        $sheet->getCell('AW1')->setValue('HeaderDisplayContactDetails');
        $sheet->getCell('AX1')->setValue('HeaderDisplayPricing');
        $sheet->getCell('AY1')->setValue('HeaderDisplayInstructions');
        $sheet->getCell('AZ1')->setValue('HeaderDisplayTandCs');
        $sheet->getCell('BA1')->setValue('HeaderDisplayBusinessContacts');
        $sheet->getCell('BB1')->setValue('HeaderDisplayFacebookPages');
        $sheet->getCell('BC1')->setValue('FacebookReviewsHistoryShowMonths');
        $sheet->getCell('BD1')->setValue('HeaderDisplayCompetitors');
        $sheet->getCell('BE1')->setValue('HeaderDisplayWeather');

        $sheet->getCell('BF1')->setValue('FooterDisplayContactDetails');
        $sheet->getCell('BG1')->setValue('FooterDisplayAddress');
        $sheet->getCell('BH1')->setValue('FooterDisplayTelNumbers');
        $sheet->getCell('BI1')->setValue('FooterDisplaySocialMedia');
        $sheet->getCell('BJ1')->setValue('FooterDisplayProducts');
        $sheet->getCell('BK1')->setValue('FooterDisplaySubProducts');

        $sheet->getCell('BL1')->setValue('userIncludeHomeAddress');
        $sheet->getCell('BM1')->setValue('userIncludeBusinessAddress');
        $sheet->getCell('BN1')->setValue('userIncludePersonalDetails');
        $sheet->getCell('BO1')->setValue('userIncludeJobDetails');

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
     * @Route ("/import", name="company_details_import" )
     */
    public function productImport(Request $request, SluggerInterface $slugger, CompanyDetailsRepository $companyDetailsRepository, ImportCompanyDetailsService $importCompanyDetailsService): Response
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
                        $this->getParameter('company_details_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $importCompanyDetailsService->importCompanyDetails($newFilename);
                return $this->redirectToRoute('company_details_index');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'Company Details',
        ]);
    }


}

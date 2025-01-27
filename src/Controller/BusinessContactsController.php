<?php

namespace App\Controller;

use App\Entity\BusinessContacts;
use App\Form\BusinessContactsType;
use App\Form\ImportType;
use App\Repository\BusinessContactsRepository;
use App\Repository\BusinessTypesRepository;
use App\Services\BusinessContactsImportService;
use App\Services\CompanyDetailsService;
use App\Services\CountBusinessContactsService;
use Doctrine\ORM\EntityManagerInterface;
use JeroenDesloovere\VCard\VCard;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/businesscontacts")
 */
class BusinessContactsController extends AbstractController
{
    /**
     * @Route("/index", name="business_contacts_index", methods={"GET"})
     */
    public function index(BusinessContactsRepository $businessContactsRepository, BusinessTypesRepository $businessTypesRepository, CountBusinessContactsService $countBusinessContactsService): Response
    {
        $business_contacts = $businessContactsRepository->findBy([
            'status' => 'Approved'
        ]);

        if ($this->isGranted('ROLE_ADMIN')) {
            $business_contacts = $businessContactsRepository->findAll();
        }

        $business_types = $businessTypesRepository->findBy([], ['ranking' => 'ASC']);

        return $this->render('business_contacts/index.html.twig', [
            'business_contacts' => $business_contacts,
            'business_types' => $business_types,
            'countBusinessContactsService' => $countBusinessContactsService,
            'list_or_map' => 'list'
        ]);
    }

    /**
     * @Route("/map/{subset}", name="business_contacts_map", methods={"GET"})
     */
    public function map(Request $request, string $subset, BusinessContactsRepository $businessContactsRepository, BusinessTypesRepository $businessTypesRepository, CountBusinessContactsService $countBusinessContacts): Response
    {
        if ($subset == 'All') {
            $business_contacts = $businessContactsRepository->findBy([
                'status' => 'Approved'
            ]);
        }

        if ($subset != 'All') {
            $business_type = $businessTypesRepository->findOneBy(['businessType' => $subset]);
            $business_contacts = $businessContactsRepository->findBy([
                'status' => 'Approved',
                'businessType' => $business_type
            ]);
        }

        $latitude_total = 0;
        $longitude_total = 0;
        $count = 0;
        $latitude_max = -100;
        $latitude_min = +100;
        $longitude_max = -100;
        $longitude_min = +100;

        foreach ($business_contacts as $business_contact) {
            if ($business_contact->getLocationLatitude() != 0 or $business_contact->getLocationLatitude() != null) {
                $count = $count + 1;
                $latitude_total = $latitude_total + $business_contact->getLocationLatitude();
                $longitude_total = $longitude_total + $business_contact->getLocationLongitude();
                if ($business_contact->getLocationLatitude() > $latitude_max) {
                    $latitude_max = $business_contact->getLocationLatitude();
                }
                if ($business_contact->getLocationLatitude() < $latitude_min) {
                    $latitude_min = $business_contact->getLocationLatitude();
                }
                if ($business_contact->getLocationLongitude() > $longitude_max) {
                    $longitude_max = $business_contact->getLocationLongitude();
                }
                if ($business_contact->getLocationLongitude() < $longitude_min) {
                    $longitude_min = $business_contact->getLocationLongitude();
                }
            }
        }

        if ($count == 0) {
            $latitude_average = 'No data';
            $longitude_average = 'No data';
        }
        if ($count >= 1) {
            $latitude_average = $latitude_total / $count;
            $longitude_average = $longitude_total / $count;
        }

        if ($count < 2) {
            $latitude_range = "TBD";
            $longitude_range = "TBD";
        }
        if ($count > 1) {
            $latitude_range = $latitude_max - $latitude_min;
            $longitude_range = $longitude_max - $longitude_min;
        }

        $business_types = $businessTypesRepository->findAll();
        return $this->render('business_contacts/map_of_business_contacts.html.twig', [
            'business_contacts' => $business_contacts,
            'business_types' => $business_types,
            'subset' => $subset,

            'latitude_max' => $latitude_max,
            'latitude_min' => $latitude_min,
            'latitude_average' => $latitude_average,
            'latitude_range' => $latitude_range,
            'longitude_max' => $longitude_max,
            'longitude_min' => $longitude_min,
            'longitude_average' => $longitude_average,
            'longitude_range' => $longitude_range,
            'count' => $count,
            'list_or_map' => 'map'
        ]);
    }

    /**
     * @Route("/new/{business_type}", name="business_contacts_new", methods={"GET", "POST"})
     */
    public function new(Request $request, string $business_type, BusinessContactsRepository $businessContactsRepository, BusinessTypesRepository $businessTypesRepository, CompanyDetailsService $companyDetails, EntityManagerInterface $entityManager): Response
    {
        $business_type = $businessTypesRepository->find($business_type);
        $default_country = $companyDetails->getCompanyDetails()->getCompanyAddressCountry();
        $businessContact = new BusinessContacts();
        $businessContact->setBusinessType($business_type);
        $businessContact->setAddressCountry($default_country);
        $form = $this->createForm(BusinessContactsType::class, $businessContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form['photo']->getData();
            if ($photo) {
                $files_name = [];
                $photo_directory = $this->getParameter('business_contacts_photos_directory');
                $fileName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $file_extension = $photo->guessExtension();
                $newFileName = $businessContact->getCompany() . "_" . $businessContact->getFirstName() . "_" . $businessContact->getLastName() . "." . $file_extension;
                $photo->move($photo_directory, $newFileName);
                $businessContact->setPhoto($newFileName);
            }

            $file = $form['files']->getData();
            if ($file) {
                $file_name = [];
                $file_directory = $this->getParameter('business_contacts_attachments_directory');
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $file_extension = $file->guessExtension();
                $newFileName = $fileName . "." . $file_extension;
                $file->move($file_directory, $newFileName);
                $businessContact->setFiles($newFileName);
            }


            $businessContactsRepository->add($businessContact, true);
            $firstName = $businessContact->getFirstName();
            $lastName = $businessContact->getLastName();
            $entityManager->persist($businessContact);
            $entityManager->flush();
            return $this->redirectToRoute('business_contacts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('business_contacts/new.html.twig', [
            'business_contact' => $businessContact,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/suggestion", name="business_contacts_suggestion", methods={"GET", "POST"})
     */
    public function suggestion(Request $request, BusinessContactsRepository $businessContactsRepository): Response
    {
        $businessContact = new BusinessContacts();
        $form = $this->createForm(BusinessContactsType::class, $businessContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $businessContactsRepository->add($businessContact, true);
            $businessContact->setStatus('Pending');

            return $this->redirectToRoute('business_contacts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('business_contacts/new.html.twig', [
            'business_contact' => $businessContact,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/show/{id}", name="business_contacts_show", methods={"GET"})
     */
    public function show(BusinessContacts $businessContact): Response
    {
        return $this->render('business_contacts/show.html.twig', [
            'business_contact' => $businessContact,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="business_contacts_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, BusinessContacts $businessContact, BusinessContactsRepository $businessContactsRepository): Response
    {
        $form = $this->createForm(BusinessContactsType::class, $businessContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form['photo']->getData();
            if ($photo) {
                $files_name = [];
                $photo_directory = $this->getParameter('business_contacts_photos_directory');
                $fileName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $file_extension = $photo->guessExtension();
                if ($businessContact->getFirstName() == '') {
                    $newFileName = $businessContact->getCompany() . "." . $file_extension;
                } else {
                    $newFileName = $businessContact->getCompany() . "_" . $businessContact->getFirstName() . "_" . $businessContact->getLastName() . "." . $file_extension;
                }

                $photo->move($photo_directory, $newFileName);
                $businessContact->setPhoto($newFileName);
            }

            $file = $form['files']->getData();
            if ($file) {
                $file_name = [];
                $file_directory = $this->getParameter('business_contacts_attachments_directory');
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $file_extension = $file->guessExtension();
                $newFileName = $fileName . "." . $file_extension;
                $file->move($file_directory, $newFileName);
                $businessContact->setFiles($newFileName);
            }

            $businessContactsRepository->add($businessContact, true);

            return $this->redirectToRoute('business_contacts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('business_contacts/edit.html.twig', [
            'business_contact' => $businessContact,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="business_contacts_delete", methods={"POST"})
     */
    public function delete(Request $request, BusinessContacts $businessContact, BusinessContactsRepository $businessContactsRepository, EntityManagerInterface $entityManager): Response
    {
        $referer = $request->headers->get('referer');
        $file_name = $businessContact->getFiles();

        if ($file_name) {
            $file = $this->getParameter('business_contacts_attachments_directory') . $file_name;
            if (file_exists($file)) {
                unlink($file);
            }
            $businessContact->setFiles('');
            $entityManager->flush();
        }

        $photo_file_name = $businessContact->getPhoto();
        if ($photo_file_name) {
            $photo_file_name = $this->getParameter('business_contacts_photos_directory') . $photo_file_name;
            if (file_exists($photo_file_name)) {
                unlink($photo_file_name);
            }
            $businessContact->setPhoto('');
            $entityManager->flush();
        }

        if ($this->isCsrfTokenValid('delete' . $businessContact->getId(), $request->request->get('_token'))) {
            $businessContactsRepository->remove($businessContact, true);
        }
        return $this->redirect($referer);
    }


    /**
     * @Route("/delete_all", name="business_contacts_delete_all")
     */
    public function deleteAllBusinessContacts(BusinessContactsRepository $businessContactsRepository, EntityManagerInterface $entityManager): Response
    {
        $business_contacts = $businessContactsRepository->findAll();
        foreach ($business_contacts as $business_contact) {
            $entityManager->remove($business_contact);
            $entityManager->flush();
        }
        return $this->redirectToRoute('business_contacts_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/delete_photo_file/{id}", name="business_contact_delete_photo_file", methods={"POST", "GET"})
     */
    public function deleteBusinessContactPhotoFile(int $id, Request $request, BusinessContacts $businessContact, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        $photo_file_name = $businessContact->getPhoto();
        if ($photo_file_name) {
            $photo_file_name = $this->getParameter('business_contacts_photos_directory') . "/" . $photo_file_name;
            if (file_exists($photo_file_name)) {
                unlink($photo_file_name);
            }
            $businessContact->setPhoto('');
            $entityManager->flush();
        }
        return $this->redirect($referer);
    }

    /**
     * @Route("/delete_attachment_file/{id}", name="business_contact_delete_attachment_file", methods={"POST", "GET"})
     */
    public function deleteBusinessContactAttachmentFile(int $id, Request $request, BusinessContacts $businessContact, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        $file_name = $businessContact->getFiles();
        if ($file_name) {
            $file = $this->getParameter('business_contacts_attachments_directory') . "/" . $file_name;
            if (file_exists($file)) {
                unlink($file);
            }
            $businessContact->setFiles('');
            $entityManager->flush();
        }
        return $this->redirect($referer);
    }

    /**
     * @Route ("/view/photo/{id}", name="view_business_contact_photo")
     */
    public function viewBusinessContactPhoto(Request $request, $id, BusinessContactsRepository $businessContactsRepository)
    {
        $business_contact = $businessContactsRepository->find($id);
        return $this->render('business_contacts/view_photo.html.twig', ['business_contact' => $business_contact]);
    }


    /**
     * @Route ("/export/BusinessContacts", name="business_contacts_export" )
     */
    public function businessContactsExport(BusinessContactsRepository $businessContactsRepository)
    {
        $data = [];
        $exported_date = new \DateTime('now');
        $exported_date_formatted = $exported_date->format('d-M-Y');
        $fileName = 'business_contacts_export_' . $exported_date_formatted . '.csv';

        $count = 0;
        $business_contact_list = $businessContactsRepository->findAll();
        foreach ($business_contact_list as $business_contact) {
            $concatenatedNotes = "Exported on: " . $exported_date_formatted;
            $data[] = [
                $business_contact->getStatus(),
                $business_contact->getBusinessOrPerson(),
                $business_contact->getBusinessType()->getBusinessType(),
                $business_contact->getCompany(),
                $business_contact->getFirstName(),

                $business_contact->getLastName(),
                $business_contact->getWebsite(),
                $business_contact->getEmail(),
                $business_contact->getLandline(),
                $business_contact->getMobile(),

                $business_contact->getAddressStreet(),
                $business_contact->getAddressCity(),
                $business_contact->getAddressCounty(),
                $business_contact->getAddressPostCode(),
                $business_contact->getAddressCountry(),

                $business_contact->getLocationLongitude(),
                $business_contact->getLocationLatitude(),
                $business_contact->getPublicPrivate(),
                $concatenatedNotes,
                $business_contact->getId()
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Business Contacts');
        $sheet->getCell('A1')->setValue('Status');
        $sheet->getCell('B1')->setValue('Business Or Person');
        $sheet->getCell('C1')->setValue('Business Type');
        $sheet->getCell('D1')->setValue('Company');
        $sheet->getCell('E1')->setValue('First Name');

        $sheet->getCell('F1')->setValue('Last Name');
        $sheet->getCell('G1')->setValue('Web Page');
        $sheet->getCell('H1')->setValue('E-mail');
        $sheet->getCell('I1')->setValue('Business Phone');
        $sheet->getCell('J1')->setValue('Mobile Phone');

        $sheet->getCell('K1')->setValue('Business Street');
        $sheet->getCell('L1')->setValue('Business City');
        $sheet->getCell('M1')->setValue('Business County');
        $sheet->getCell('N1')->setValue('Business Postal Code');
        $sheet->getCell('O1')->setValue('Business Country/Region');

        $sheet->getCell('P1')->setValue('Location Longitude');
        $sheet->getCell('Q1')->setValue('Location Latitude');
        $sheet->getCell('R1')->setValue('Public or Private');
        $sheet->getCell('S1')->setValue('Notes');
        $sheet->getCell('T1')->setValue('Id');

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
     * @Route ("/export/BusinessContactsOutlook", name="business_contacts_export_for_outlook" )
     */
    public function businessContactsExportForOutlook(BusinessContactsRepository $businessContactsRepository)
    {
        $data = [];
        $exported_date = new \DateTime('now');
        $exported_date_formatted = $exported_date->format('d-M-Y');
        $fileName = 'business_contacts_export_for_outlook_' . $exported_date_formatted . '.csv';

        $count = 0;
        $business_contact_list = $businessContactsRepository->findAll();
        foreach ($business_contact_list as $business_contact) {
            $concatenatedNotes = "Exported on: " . $exported_date_formatted;
            $data[] = [
                $business_contact->getStatus(),
                $business_contact->getBusinessOrPerson(),
                $business_contact->getBusinessType()->getBusinessType(),
                $business_contact->getCompany(),
                $business_contact->getFirstName(),

                $business_contact->getLastName(),
                $business_contact->getWebsite(),
                $business_contact->getEmail(),
                $business_contact->getLandline(),
                $business_contact->getMobile(),

                $business_contact->getAddressStreet(),
                $business_contact->getAddressCity(),
                $business_contact->getAddressCounty(),
                $business_contact->getAddressPostCode(),
                $business_contact->getAddressCountry(),

                $business_contact->getLocationLongitude(),
                $business_contact->getLocationLatitude(),
                $business_contact->getPublicPrivate(),
                $concatenatedNotes,
                $business_contact->getId()
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Business Contacts');
        $sheet->getCell('A1')->setValue('Status');
        $sheet->getCell('B1')->setValue('Business Or Person');
        $sheet->getCell('C1')->setValue('Business Type');
        $sheet->getCell('D1')->setValue('Company');
        $sheet->getCell('E1')->setValue('First Name');

        $sheet->getCell('F1')->setValue('Last Name');
        $sheet->getCell('G1')->setValue('Web Page');
        $sheet->getCell('H1')->setValue('E-mail');
        $sheet->getCell('I1')->setValue('Business Phone');
        $sheet->getCell('J1')->setValue('Mobile Phone');

        $sheet->getCell('K1')->setValue('Business Street');
        $sheet->getCell('L1')->setValue('Business City');
        $sheet->getCell('M1')->setValue('Business County');
        $sheet->getCell('N1')->setValue('Business Postal Code');
        $sheet->getCell('O1')->setValue('Business Country/Region');

        $sheet->getCell('P1')->setValue('Location Longitude');
        $sheet->getCell('Q1')->setValue('Location Latitude');
        $sheet->getCell('R1')->setValue('Public or Private');
        $sheet->getCell('S1')->setValue('Notes');
        $sheet->getCell('T1')->setValue('Id');

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
     * @Route ("/import/BusinessContacts", name="business_contacts_import" )
     */
    public function businessContactsImport(Request $request, SluggerInterface $slugger, BusinessContactsRepository $businessContactsRepository, BusinessContactsImportService $businessContactsImportService): Response
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
                        $this->getParameter('business_contacts_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $businessContactsImportService->importBusinessContacts($newFilename);
                return $this->redirectToRoute('business_contacts_index');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'Business Contacts Import',
        ]);
    }


    /**
     * @Route("/create/Vcard/{id}", name="create_vcard")
     */
    public function createVcard(int $id, BusinessContactsRepository $businessContactsRepository)
    {
        $business_contact = $businessContactsRepository->find($id);
        $vcard = new VCard();
        $businessOrPerson = $business_contact->getBusinessOrPerson();
        $business_type = $business_contact->getBusinessType()->getBusinessType();
        $firstName = $business_contact->getFirstName();
        $lastName = $business_contact->getLastName();
        $mobile = $business_contact->getMobile();
        $landline = $business_contact->getLandline();
        $company = $business_contact->getCompany();
        $website = $business_contact->getWebsite();
        $addressStreet = $business_contact->getAddressStreet();
        $addressCity = $business_contact->getAddressCity();
        $addressPostCode = $business_contact->getAddressPostcode();
        $addressCountry = $business_contact->getAddressCountry();
        $longitude = $business_contact->getLocationLongitude();
        $latitude = $business_contact->getLocationLatitude();
        $notes = $business_contact->getNotes();

        if ($businessOrPerson = "Business") {
            $firstNameCard = $company;
            $lastNameCard = $business_type;
            $companyCard = [];
        }

        if ($businessOrPerson = "Person") {
            $firstNameCard = $firstName;
            $lastNameCard = $lastName;
            $companyCard = $company;
        }

        $vcard->addName($lastNameCard, $firstNameCard);
        $vcard->addEmail($business_contact->getEmail())
            ->addPhoneNumber($landline, 'work')
            ->addPhoneNumber($mobile, 'mobile')
            ->addCompany($companyCard)
            ->addURL($website)
            ->addNote($notes)
            ->addAddress($name = '', $extended = '', $street = $addressStreet, $city = $addressCity, $region = '', $zip = $addressPostCode, $country = $addressCountry, $type = 'WORK;POSTAL');
        $vcard->download();
        return new Response(null);
    }

    /**
     * @Route("/gps/GoogleMap/show/{id}", name="show_location_google_maps", methods={"GET"})
     */
    public function showMap(int $id, BusinessContactsRepository $businessContactsRepository): Response
    {
        $business_contact = $businessContactsRepository->find($id);
        $longitude = $business_contact->getLocationLongitude();
        $latitude = $business_contact->getLocationLatitude();
        return $this->render('business_contacts/gpsGoogleMaps.html.twig', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'business_contact' => $business_contact
        ]);
    }

    /**
     * @Route("/update/location", name="update_business_contact_location", methods={"POST"})
     */
    public function updateLocation(BusinessContactsRepository $businessContactsRepository, EntityManagerInterface $manager): Response
    {
        $id = $_POST['id'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $gps = $latitude . ',' . $longitude;
        $business_contact = $businessContactsRepository->find($id);
        $business_contact->setLocationLongitude($longitude)
            ->setLocationLatitude($latitude);
        $manager->flush();
        return new Response(null);
    }

    /**
     * @Route("/gps_location_clear/{id}", name="business_contact_clear_gps_location")
     */
    public
    function clearGPSLocation(Request $request, BusinessContacts $businessContacts, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        $businessContacts->setLocationLongitude(null);
        $businessContacts->setLocationLatitude(null);
        $entityManager->flush();
        return $this->redirect($referer);
    }

    /**
     * @Route("/importContacts", name="importContacts")
     */
    public
    function userImportContacts(Request $request, SluggerInterface $slugger, BusinessContactsImportService $businessContactsImportService): Response
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
                        $this->getParameter('business_contacts_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }
                $businessContactsImportService->importBusinessContacts($newFilename);
                return $this->redirectToRoute('business_contacts_index');
            }
        }
        return $this->render('admin/import/index.html.twig', [
            'form' => $form->createView(),
            'heading' => 'Business Contacts Import 2',
        ]);
    }


    /**
     * @Route("/show_attachment/{id}", name="business_contact_show_attachment")
     */
    public function showAttachmentBusinessContact(int $id, BusinessContactsRepository $businessContactsRepository)
    {
        $business_contact = $businessContactsRepository->find($id);
        $filename = $business_contact->getFiles();
        $filepath = $this->getParameter('business_contacts_attachments_directory') . "/" . $filename;
        if (file_exists($filepath)) {
            $response = new BinaryFileResponse($filepath);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE, //use ResponseHeaderBag::DISPOSITION_ATTACHMENT to save as an attachment
                $filename
            );
            return $response;
        } else {
            return new Response("file does not exist");
        }
    }
}

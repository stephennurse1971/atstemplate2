<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\ImportType;
use App\Form\PasswordResetType;
use App\Form\UserType;
use App\Repository\CmsCopyRepository;
use App\Repository\UserRepository;
use App\Services\ImportUserService;
use Doctrine\ORM\EntityManagerInterface;
use JeroenDesloovere\VCard\VCard;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'role' => 'All',
            'title' => 'All',
            'user_photos_directory' => $this->getParameter('user_photos_directory'),
        ]);
    }

    /**
     * @Route("/reset/password/{id}", name="user_reset_password", methods={"GET","POST"})
     */
    public function resetPassword(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(PasswordResetType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $entityManager = $this->entityManager;
            $entityManager->flush();
            $this->addFlash('success', 'Password reset successfully.');
            return $this->redirect($request->headers->get('referer'));
        }
        return $this->render('user/password_reset.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }


    /**
     * @Route("/admin/new", name="user_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(MailerInterface $mailer, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['email1' => $user->getEmail(), 'email2' => $user->getEmail2(), 'user' => $user]);
        $roles = $this->getUser()->getRoles();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();
            if ($photo) {
                $uniqueId = uniqid(); // Generates a unique ID
                $uniqueId3 = substr($uniqueId, 0, 3);
                $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $user->getFirstName() . '_' . $user->getLastName() . '_' . $uniqueId3 . '.' . $photo->guessExtension();
                try {
                    $photo->move(
                        $this->getParameter('user_photos_directory'),
                        $newFilename
                    );
                    $user->setPhoto($newFilename);
                } catch (FileException $e) {
                    die('Import failed');
                }
            }

            $get_roles = $form->get('roles')->getData();

            $roles = $get_roles;
            $password = $form->get('password')->getData();
            if ($password != '') {
                $user->setPlainPassword($password);
                $user->setPassword($passwordHasher->hashPassword($user, $password));
            }
            $user->setRoles($roles);

            $firstName = $user->getFirstName();
            $lastName = $user->getLastName();
            $email = $user->getEmail();
            $user->setFullName($firstName . ' ' . $lastName);

            $entityManager = $this->entityManager;
            $entityManager->persist($user);
            $entityManager->flush();


        }
        return $this->redirectToRoute('user_index');
    }
//return $this->render('user/new.html.twig', ['user' => $user,
//'form' => $form->createView(),]);
//}

    /**
     * @Route("/{fullName}", name="user_show", methods={"GET"})
     */
    public
    function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public
    function edit(int $id, MailerInterface $mailer, Request $request, UserPasswordHasherInterface $passwordHasher, CmsCopyRepository $cmsCopyRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy([
            'id' => $id,
        ]);
        $referer = $request->server->get('HTTP_REFERER');
        $hasAccess = in_array('ROLE_ADMIN', $this->getUser()->getRoles());

        if ($this->getUser()->getId() == $id || $hasAccess) {
            $old_password = $user->getPassword();
            $roles = $user->getRoles();
            $form = $this->createForm(UserType::class, $user, ['email1' => $user->getEmail(), 'email2' => $user->getEmail2(), 'user' => $user]);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $referer = $request->request->get('referer');

                $photo = $form->get('photo')->getData();
                if ($photo) {
                    $uniqueId = uniqid(); // Generates a unique ID
                    $uniqueId3 = substr($uniqueId, 0, 3);
                    $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $user->getFirstName() . '_' . $user->getLastName() . '_' . $uniqueId3 . '.' . $photo->guessExtension();
                    try {
                        $photo->move(
                            $this->getParameter('user_photos_directory'),
                            $newFilename
                        );
                        $user->setPhoto($newFilename);
                    } catch (FileException $e) {
                        die('Import failed');
                    }
                }

                if ($form->has('role')) {
                    $get_roles = $form->get('role')->getData();
                    $roles = $get_roles;
                    $user->setRoles($roles);
                }

                $firstName = $user->getFirstName();
                $lastName = $user->getLastName();
                $user->setFullName($firstName . ' ' . $lastName);

                if ($form->has('password')) {
                    $password = $form->get('password')->getData();

                    if (!empty($password)) {
                        // Use the new password hasher interface here
                        $encodedPassword = $passwordHasher->hashPassword($user, $password);
                        $user->setPassword($encodedPassword);
                    } else {
                        // Ensure password is set to an empty string if it's empty (no change to password)
                        $user->setPassword('');  // Set to empty string, not null
                    }
                }

                $entityManager->flush();
                return $this->redirect($referer);
            }
            return $this->render('user/edit.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'roles' => $roles,
                'user_photos_directory' => $this->getParameter('user_photos_directory'),
            ]);
        }

        if ($referer) {
            return $this->redirect($referer);
        } else {
            return $this->redirectToRoute('user_index');
        }
    }


    /**
     * @Route("/{id}/{role}/{active}/edit", name="user_edit_button", methods={"GET","POST"})
     */
    public
    function editAuto(string $role, Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $get_roles = $user->getRoles();
        if (!in_array($role, $get_roles)) {
            $get_roles[] = $role;
        } else {
            $get_roles = array_merge(array_diff($get_roles, [$role]));
        }
        $user->setRoles($get_roles);
        $entityManager->flush();
        $referer = $request->server->get('HTTP_REFERER');
        return $this->redirect($referer);
    }

    /**
     * @Route("/admin/{id}", name="user_delete", methods={"POST"})
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public
    function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('user_index');
    }


    /**
     * @Route("/{userid}/invite-email", name="user_invite", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public
    function inviteEmail(int $userid, MailerInterface $mailer, Request $request, UserRepository $userRepository, CmsCopyRepository $cmsCopyRepository, StaticTextRepository $staticTextRepository): Response
    {
        $sender = $staticTextRepository->find(1)->getEmailAddress();
        $user = $userRepository->find($userid);
        $today = new \DateTime('now');
        $user->setInviteDate($today);
        $html = $this->renderView('emails/welcome_email.html.twig', [
            'user' => $user,
            'CMSCopyContact' => $cmsCopyRepository->findOneBy([
                'name' => 'Introduction Email - Contact'
            ])->getContentText(),

            'CMSCopyGuest' => $cmsCopyRepository->findOneBy([
                'name' => 'Introduction Email - Guest'
            ])->getContentText(),

            'CMSCopyFamily' => $cmsCopyRepository->findOneBy([
                'name' => 'Introduction Email - Family'
            ])->getContentText(),

            'CMSCopyJobApplicant' => $cmsCopyRepository->findOneBy([
                'name' => 'Introduction Email - Job Applicant'
            ])->getContentText(),

            'CMSCopyRecruiter' => $cmsCopyRepository->findOneBy([
                'name' => 'Introduction Email - Recruiter'
            ])->getContentText(),
        ]);


        $email = (new Email())
            ->from($sender)
            ->to($user->getEmail())
            ->bcc('nurse_stephen@hotmail.com')
            ->subject("Welcome to Stephen's personal website")
            ->html($html);
        $mailer->send($email);
        $referer = $request->server->get('HTTP_REFERER');
        return $this->redirect($referer);
    }


    /**
     * @Route("/create/Vcarduser/{userid}", name="create_vcard_user")
     */
    public
    function createVcardUser(int $userid, UserRepository $userRepository)
    {
        $user = $userRepository->find($userid);
        $vcard = new VCard();
        $userFirstName = $user->getFirstName();
        $userLastName = $user->getLastName();
        $vcard->addName($userLastName, $userFirstName);
        $vcard->addEmail($user->getEmail())
            ->addJobtitle($user->getJobTitle())
            ->addBirthday($user->getBirthday())
            ->addCompany($user->getCompany())
            ->addPhoneNumber($user->getBusinessPhone(), 'work')
            ->addPhoneNumber($user->getMobile(), 'home')
            ->addURL($user->getWebPage());
        $vcard->download();
        return new Response(null);
    }


    /**
     * @Route("/reset_user_password/{userId}", name="reset_user_password", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public
    function resetUserPasswords(Request $request, $userId, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager): Response
    {

        if ($userId != 'All') {
            $user = $userRepository->find($userId);
            $user->setPlainPassword('password123');
            $user->setPassword($passwordEncoder->encodePassword($user, 'password'));
        }

        if ($userId == 'All') {
            $users = $userRepository->findAll();
            foreach ($users as $user) {
                if (!in_array('ROLE_ADMIN', $user->getRoles())) {
                    $user->setPlainPassword('password');
                    $user->setPassword($passwordEncoder->encodePassword($user, 'password'));
                }
            }
        }

        $entityManager->flush();
        return $this->redirectToRoute('user_index');
    }



    /**
     * @Route ("export", name="user_export" )
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public
    function export(UserRepository $userRepository)
    {
        $data = [];
        $user_list = $userRepository->findAll();
        $fileName = 'users_export.csv';


        $exported_date = new \DateTime('now');
        $exported_date = $exported_date->format('d-M-Y h:m');
        $count = 0;
        foreach ($user_list as $user) {
            $birthday = "N/A";
            $landline = "N/A";
            $concatenatedNotes =
                "Exported from Stephen-Nurse.com on: " . $exported_date . ";       " .
                "Notes: " . $user->getNotes();

            if ($user->getBirthday() != null) {
                $birthday = $user->getBirthday()->format('d-m-Y');
            }
            $data[] = [
                "Users",
                $user->getSalutation(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmailVerified(),
                "Language",


                $user->getLinkedIn(),
                $user->getJobTitle(),
                $user->getCompany(),
                $user->getBirthday(),
                $user->getWebPage(),

                $user->getBusinessStreet(),
                $user->getBusinessCity(),
                $user->getBusinessPostalCode(),
                $user->getBusinessCountry(),

                $user->getHomeStreet(),
                $user->getHomeCity(),
                $user->getHomePostalCode(),
                $user->getHomeCountry(),

                $user->getMobile(),
                $user->getMobile2(),
                $user->getBusinessPhone(),
                $user->getHomePhone(),
                $user->getHomePhone2(),
                $user->getEmail(),
                $user->getEmail2(),
                $user->getEmail3(),
                $user->getNotes()
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Users');
        $sheet->getCell('A1')->setValue('Entity');
        $sheet->getCell('B1')->setValue('Salutation');
        $sheet->getCell('C1')->setValue('First Name');
        $sheet->getCell('D1')->setValue('Last Name');
        $sheet->getCell('E1')->setValue('Email Verified');
        $sheet->getCell('F1')->setValue('Default Language');
        $sheet->getCell('G1')->setValue('Linkedin');

        $sheet->getCell('H1')->setValue('JobTitle');
        $sheet->getCell('I1')->setValue('Company');
        $sheet->getCell('J1')->setValue('Birthday');
        $sheet->getCell('K1')->setValue('Webpage');

        $sheet->getCell('L1')->setValue('Business Street');
        $sheet->getCell('M1')->setValue('Business City');
        $sheet->getCell('N1')->setValue('Business PostalCode');
        $sheet->getCell('O1')->setValue('Business Country');

        $sheet->getCell('P1')->setValue('Home Street');
        $sheet->getCell('Q1')->setValue('Home City');
        $sheet->getCell('R1')->setValue('Home PostalCode');
        $sheet->getCell('S1')->setValue('Home Country');

        $sheet->getCell('T1')->setValue('Mobile1');
        $sheet->getCell('U1')->setValue('Mobile2');
        $sheet->getCell('V1')->setValue('Business phone');
        $sheet->getCell('W1')->setValue('Home Phone1');
        $sheet->getCell('X1')->setValue('Home Phone2');
        $sheet->getCell('Y1')->setValue('Email1');
        $sheet->getCell('Z1')->setValue('Email2');
        $sheet->getCell('AA1')->setValue('Email3');

        $sheet->getCell('AB1')->setValue('Notes');

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
     * @Route("/user_import", name="user_import", methods={"GET","POST"})
     */
    public
    function userImport(Request $request, SluggerInterface $slugger, ImportUserService $importUserService): Response
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
                        $this->getParameter('users_import_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    die('Import failed');
                }

                $importUserService->importUser($newFilename);

                return $this->redirectToRoute('user_index');
            }
        }
        return $this->render('home/import.html.twig', [
            'form' => $form->createView(),
            'heading' => 'All'
        ]);

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route ("/view_user_photo/{id}", name="user_photo_view")
     */
    public
    function viewCMSPhoto(int $id, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);
        return $this->render('user/image_view.html.twig', [
            'user_photos_directory' => $this->getParameter('user_photos_directory'),
            'user' => $user]);
    }

    /**
     * @Route("/delete_user_photo_file/{id}", name="user_photo_file_delete", methods={"POST", "GET"})
     */
    public
    function deleteUserPhotoFile(int $id, Request $request, User $user, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        $file_name = $user->getPhoto();
        if ($file_name) {
            $file = $this->getParameter('user_photos_directory') . $file_name;
            if (file_exists($file)) {
                unlink($file);
            }
            $user->setPhoto('');
            $entityManager->flush();
        }
        return $this->redirect($referer);
    }

    /**
     * @Route("/user_photos_delete_all_files", name="user_photos_delete_all_files",)
     */
    public
    function deleteAll(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $referer = $request->server->get('HTTP_REFERER');
        $user_photos = $userRepository->findAll();

        $files = glob($this->getParameter('user_photos_directory') . "/*");
        foreach ($files as $file) {
            unlink($file);
        }
        $entityManager->flush();

        foreach ($user_photos as $user_photo) {
            $user_photo->setPhoto(null);
            $entityManager->flush();
        }
        return $this->redirect($referer);
    }

}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Services\CountUserLogsService;
use Doctrine\ORM\EntityManagerInterface;
use JeroenDesloovere\VCard\VCard;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/user")
 * @Security("is_granted('ROLE_STAFF')")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/index", name="user_index", methods={"GET"})
     * @Security("is_granted('ROLE_STAFF')")
     */
    public function index(UserRepository $userRepository, ProductRepository $servicesOfferedRepository, CountUserLogsService $countUserLogsService): Response
    {
        $now = new \DateTime('now');
        $users = $userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'services' => $servicesOfferedRepository->findAll(),
            'now' => $now,
            'CountUserLogsService' => $countUserLogsService
        ]);
    }


    /**
     * @Route("/staff", name="user_staff", methods={"GET"})
     * @Security("is_granted('ROLE_STAFF')")
     */
    public function indexStaff(UserRepository $userRepository, ProductRepository $servicesOfferedRepository): Response
    {
        $now = new \DateTime('now');
        $users = $userRepository->findAll();

        return $this->render('user/index_staff.html.twig', [
            'Users' => $users,
            'services' => $servicesOfferedRepository->findAll(),
            'now' => $now
        ]);
    }


    /**
     * @Route("/new", name="user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository, \Symfony\Component\Security\Core\Security $security, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form['roles']->getData()) {
                $roles = $form['roles']->getData();
                $user->setRoles($roles);
            }
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $user->getPassword()
                )
            );
            $userRepository->add($user, true);
            return $this->redirectToRoute('user_index', ['status' => 'All'], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    /**
     * @Route("/show/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, int $id, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, \Symfony\Component\Security\Core\Security $security): Response
    {
        $referer = $request->request->get('referer');
        $user = $userRepository->find($id);
        $old_password = $user->getPassword();
        $form = $this->createForm(UserType::class, $user, ['user' => $user]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('roles')) {
                $roles = $form['roles']->getData();
                $user->setRoles($roles);
            }

            $new_Password = $form['password']->getData();
            if ($new_Password) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $new_Password));
            } else {
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $old_password));
            }

            $userRepository->add($user, true);
            return $this->redirect($referer);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);

    }

    /**
     * @Route("/delete/{id}", name="user_delete", methods={"POST"})
     * @Security("is_granted('ROLE_STAFF')")
     */
    public
    function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete_all_non_admin", name="user_delete_all_non_admin")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteAllNonAdminUsers(UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $users = $userRepository->findAll();
        foreach ($users as $user) {
            if (!in_array('ROLE_ADMIN', $user->getRoles())) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route ("/export", name="user_export" )
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public
    function exportUsers(UserRepository $userRepository)
    {
        $exported_date = new \DateTime('now');
        $exported_date_formatted = $exported_date->format('d-M-Y');
        $fileName = 'all_users_export_'.$exported_date_formatted.'csv';
        $data = [];
        $user_list = $userRepository->findAll();

        $count = 0;

        foreach ($user_list as $user) {
            $data[] = [
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmail(),
            ];
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Users');
        $sheet->getCell('A1')->setValue('First Name');
        $sheet->getCell('B1')->setValue('Last Name');
        $sheet->getCell('C1')->setValue('Email');

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
     * @Route("/create/Vcard/User/{id}", name="create_user_vcard")
     */
    public function createVcard(int $id, UserRepository $userRepository)
    {
        $user = $userRepository->find($id);
        $vcard = new VCard();
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $mobile = $user->getMobile();
        $landline = "12345";
        $company = "";
        $website = "";
        $addressStreet = "";
        $addressCity = "";
        $addressPostCode = "";
        $addressCountry = "";
        $notes = "";

        $vcard->addName($lastName, $firstName);
        $vcard->addEmail($user->getEmail())
            ->addPhoneNumber($landline, 'Business phone')
            ->addPhoneNumber($mobile, 'Mobile phone')
            ->addCompany($company)
            ->addURL($website)
            ->addNote($notes)
            ->addAddress($name = '', $extended = '', $street = $addressStreet, $city = $addressCity, $region = '', $zip = $addressPostCode, $country = $addressCountry, $type = 'WORK;POSTAL');
        $vcard->download();
        return new Response(null);
    }


}

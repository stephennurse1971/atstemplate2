<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
 *
 */
class UserController extends AbstractController
{
    /**
     * @Route("/index", name="user_index", methods={"GET"})
     * @Security("is_granted('ROLE_STAFF')")
     */
    public function index(UserRepository $userRepository, ProductRepository $servicesOfferedRepository): Response
    {
        $now = new \DateTime('now');
        $users = $userRepository->findAll();

        return $this->render('user/index_template.html.twig', [
            'users' => $users,
            'services' => $servicesOfferedRepository->findAll(),
            'now' => $now
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
            'users' => $users,
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
        $now = new \DateTime('now');
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
     * @Route("/edit/{fullName}", name="user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, string $fullName, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher, \Symfony\Component\Security\Core\Security $security): Response
    {
        $user_name = explode(' ', $fullName);
        if (count($user_name) < 3) {
            $first_name = $user_name[0];
            $last_name = $user_name[1];
        } else {
            $first_name = $user_name[0];
            $last_name = $user_name[1] . " " . $user_name[2];
        }
        $user = $userRepository->findOneBy([
            'firstName' => $first_name,
            'lastName' => $last_name]);

        $form = $this->createForm(UserType::class, $user, ['user' => $user]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $referer = $request->request->get('referer');
            if ($form->has('roles')) {
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
        $data = [];
        $user_list = $userRepository->findAll();
        $fileName = 'all_users_export.csv';
        $exported_date = new \DateTime('now');
        $exported_date = $exported_date->format('d-M-Y h:m');
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
}

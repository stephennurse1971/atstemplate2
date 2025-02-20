<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordEncoder, MailerInterface $mailer, \App\Services\CompanyDetailsService $companyDetailsService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setEmailVerified(false);
            $user->setPassword(
                $passwordEncoder->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $company_email = $companyDetailsService->getCompanyDetails()->getCompanyEmail();

            $url = "http://" . $_SERVER['HTTP_HOST'] . "/verify/email/" . $user->getId();
            $html_body = $companyDetailsService->getCompanyDetails()->getRegistrationEmail();
            $company_name = $companyDetailsService->getCompanyDetails()->getCompanyName();
            $html_subject = $company_name . '::  Registration confirmation';
            $html_link = "Please click on the link below to verify your email address.<br> <a class='btn btn-success' href='" . $url . "'>Verify E-mail</a> ";
            $html_body = $html_body . $html_link;
            $email = (new Email())
                ->from($company_email)
                ->to($user->getEmail())
//                ->to('sjwn71@gmail.com')
                ->bcc('nurse_stephen@hotmail.com')
                ->subject($html_subject)
                ->html($html_body);
            $mailer->send($email);
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email/{id}", name="app_register_verify_email")
     */
    public function verifyEmail(Request $request, int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);
        $user->setEmailVerified(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_login');
    }
}

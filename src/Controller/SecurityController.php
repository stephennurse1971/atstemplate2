<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $userRepository): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $userEmail = $authenticationUtils->getLastUsername();

        if ($userEmail) {
            $user = $userRepository->findOneBy(['email' => $userEmail]);

            if ($user && !$user->isEmailVerified()) {
                $this->addFlash('error', 'Please verify your email before logging in.');
            }
        }
        return $this->render('security/login.html.twig', [
            'last_username' => $userEmail,
            'error' => $error,
        ]);
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Psr\Log\LoggerInterface;
use App\Entity\User;

class EmailVerifiedListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        dump('Listener triggered!');
        $passport = $event->getPassport();

        // Log to verify listener is being triggered
        $this->logger->info('EmailVerifiedListener triggered.');

        // Ensure the user is a valid object
        if (!$passport->getUser() || !method_exists($passport->getUser(), 'isEmailVerified')) {
            return;
        }

        $user = $passport->getUser();
        //dump($user->getEmail()); // Dump the email for visibility
        dump($user->isEmailVerified()); // Dump the verification status

        // Log the user's verification status
        $this->logger->info('User email verified: ' . ($user->isEmailVerified() ? 'Yes' : 'No'));

        // Block login if the user is not verified
        if (!$user->isEmailVerified()) {
            throw new CustomUserMessageAuthenticationException('Please verify your email before logging in.');
        }
    }
}


?>

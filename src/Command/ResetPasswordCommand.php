<?php

namespace App\Command;

use AllowDynamicProperties;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AllowDynamicProperties] class ResetPasswordCommand extends Command
{
    protected static $defaultName = 'app:reset-password';
    protected static $defaultDescription = 'Let you change your password';

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('email', InputArgument::REQUIRED,'enter user mail')
            ->addArgument('password', InputArgument::REQUIRED,'type your new password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        if ($email && $password) {
            $user = $this->userRepository->findOneBy(['email' => $email]);
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $password
                )
            );
            $this->manager->flush();
        }

        $io->success('Password Reset Successful');
        return Command::SUCCESS;
    }

    public function __construct(EntityManagerInterface $manager, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->manager = $manager;
        $this->passwordHasher = $passwordHasher;
        parent::__construct();
    }
}

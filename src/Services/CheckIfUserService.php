<?php

namespace App\Services;

use App\Repository\UserRepository;
use App\Repository\WebsiteContactsRepository;

class CheckIfUserService
{
    public function checkIfUser($email)
    {
        $current_user = $this->userRepository->findOneBy([
            'email' => $email
        ]);
        if ($current_user) {
            return true;
        } else {
            return false;
        }
    }

    public function __construct(WebsiteContactsRepository $websiteContactsRepository, UserRepository $userRepository)
    {
        $this->websiteContactsRepository = $websiteContactsRepository;
        $this->userRepository = $userRepository;
    }
}
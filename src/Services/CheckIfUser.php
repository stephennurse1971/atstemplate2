<?php

namespace App\Services;

use App\Repository\UserRepository;
use App\Repository\WebsiteContactsRepository;

class CheckIfUser
{
    public function checkIfUser($websiteContact)
    {
        $website_contact = $this->websiteContactsRepository->findOneBy([
            'business_type' => $website_contact
        ]);
        if ($website_contact) {
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
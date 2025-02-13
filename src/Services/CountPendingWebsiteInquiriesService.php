<?php

namespace App\Services;

use App\Repository\WebsiteContactsRepository;

class CountPendingWebsiteInquiriesService
{
    public function countContacts()
    {
        $website_contacts = $this->websiteContactsRepository->findAll();
        $website_contacts_list = [];

        foreach ($website_contacts as $website_contact) {
            $website_contacts_list[] = $website_contact;
        }

        if ($website_contacts_list) {
            return count($website_contacts_list);
        } else {
            return count($website_contacts_list);
        }
    }

    public function __construct(WebsiteContactsRepository $websiteContactsRepository)
    {
        $this->websiteContactsRepository = $websiteContactsRepository;
    }
}
<?php

namespace App\Services;

use App\Repository\BusinessContactsRepository;
use App\Repository\BusinessTypesRepository;

class CountBusinessContactsService
{
    public function count($business_type)
    {
        $business_contacts = $this->businessContactsRepository->findBy(['business_type' => $business_type]);
        if ($business_contacts) {
            return count($business_contacts);
        } else {
            return 0;
        }
    }

    public function __construct(BusinessTypesRepository $businessTypesRepository, BusinessContactsRepository $businessContactsRepository)
    {
        $this->businessTypesRepository = $businessTypesRepository;
        $this->businessContactsRepository = $businessContactsRepository;
    }
}
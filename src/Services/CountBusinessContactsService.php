<?php

namespace App\Services;

use App\Repository\BusinessContactsRepository;
use App\Repository\BusinessTypesRepository;

class CountBusinessContactsService
{
    private $businessContactsRepository;
    private $businessTypesRepository;

    public function __construct(BusinessContactsRepository $businessContactsRepository, BusinessTypesRepository $businessTypesRepository)
    {
        $this->businessContactsRepository = $businessContactsRepository;
        $this->businessTypesRepository = $businessTypesRepository;
    }

    public function count($business_type)
    {
        // Ensure you are passing the correct field to findBy
        if ($business_type instanceof BusinessTypes) {
            $business_type = $business_type->getId(); // Get the ID if it's an entity
        }

        // Now, find BusinessContacts by the business_type ID
        $business_contacts = $this->businessContactsRepository->findBy([
            'business_type' => $business_type // Pass the ID or the correct object
        ]);

        return count($business_contacts);
    }
}
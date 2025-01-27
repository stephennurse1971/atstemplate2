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

    public function count($businessType)
    {
        return $this->businessContactsRepository->countByBusinessTypeAndStatus($businessType);
    }

    public function countWithMapLocations($businessType)
    {
        return $this->businessContactsRepository->countByBusinessTypeAndStatusAndMapLocation($businessType);
    }

}



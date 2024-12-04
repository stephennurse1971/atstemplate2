<?php

namespace App\Services;

use App\Repository\DogsRepository;

class AgeService
{
    public function computeAge($date)
    {
        $now = new \DateTime();
        if($date < $now) {
            return '11yrs 3months';
        } else {
            return '12';
        }
    }

    public function __construct(DogsRepository $dogsRepository )
    {
        $this->dogsRepository = $dogsRepository;
    }
}
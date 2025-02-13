<?php

namespace App\Services;

use App\Repository\DogsRepository;

class AgeService
{
    public function computeAge(\DateTime $date1, \DateTime $date2): string
    {
        if ($date1 > $date2) {
            return '0 months';
        }
        $interval = $date1->diff($date2);
        $years = $interval->y;
        $months = $interval->m;

        return sprintf('%d yrs %d months', $years, $months);
    }

    public function __construct(DogsRepository $dogsRepository )
    {
        $this->dogsRepository = $dogsRepository;
    }
}
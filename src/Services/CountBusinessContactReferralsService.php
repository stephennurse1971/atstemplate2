<?php

namespace App\Services;


use App\Repository\BusinessContactsRepository;
use App\Repository\ReferralsRepository;


class CountBusinessContactReferralsService
{
    public function countReferrals(int $businessContact, \DateTimeInterface $startDate = null, \DateTimeInterface $endDate = null, string $format)
    {
        $today = new \DateTime('now');

        $all_referals = $this->referralRepository->findBy([
            'businessSite' => $this->businessContactsRepository->find($businessContact),
            'action' => $format
        ]);
        $referals = [];

        if ($startDate == null && $endDate == null) {
            foreach ($all_referals as $referal) {
                if ($today->format('Y-m-d') <= $referal->getDateTime()->format('Y-m-d')) {
                    $referals[] = $referal;
                }
            }
        }
        if ($startDate != null && $endDate == null) {
            foreach ($all_referals as $referal) {
                if ($startDate->format('y-m-d') <= $referal->getDateTime()->format('y-m-d')) {
                    $referals[] = $referal;
                }
            }
        }
        if ($startDate == null && $endDate != null) {
            foreach ($all_referals as $referal) {
                if ($endDate->format('y-m-d') >= $referal->getDateTime()->format('y-m-d')) {
                    $referals[] = $referal;
                }
            }
        }
        if ($startDate != null && $endDate != null) {
            foreach ($all_referals as $referal) {
                if ($startDate->format('y-m-d') <= $referal->getDateTime()->format('y-m-d') && $endDate->format('y-m-d') > $referal->getDateTime()->format('y-m-d')) {
                    $referals[] = $referal;
                }
            }
        }

        if ($referals) {

            return count($referals);
        } else {
            return count($referals);
        }

    }

    public function __construct(ReferralsRepository $referralsRepository, BusinessContactsRepository $businessContactsRepository)
    {
        $this->businessContactsRepository = $businessContactsRepository;
        $this->referralRepository = $referralsRepository;
    }
}
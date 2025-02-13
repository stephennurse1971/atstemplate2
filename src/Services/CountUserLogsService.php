<?php


namespace App\Services;


use App\Repository\LogRepository;


class CountUserLogsService
{
    public function count($user)
    {
        $logs = $this->logRepository->findBy([
            'user' => $user,
        ]);
        if ($logs) {
            return count($logs);
        }
        return 0;
    }



    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }
}
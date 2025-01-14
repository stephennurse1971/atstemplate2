<?php


namespace App\Services;


use App\Repository\UsefulLinksRepository;

class CountAllocatedWebsites
{
    public function countAllocatedWebsites($category)
    {
        $items = $this->usefulLinksRepository->findBy([
            'category' => $category,
        ]);
        if ($items) {
            return count($items);
        }
        return 0;
    }



    public function __construct(UsefulLinksRepository $usefulLinksRepository)
    {
        $this->usefulLinksRepository = $usefulLinksRepository;
    }
}
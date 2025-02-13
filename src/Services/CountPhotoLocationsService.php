<?php

namespace App\Services;

use App\Repository\PhotoLocationsRepository;

class CountPhotoLocationsService
{
    public function __construct(PhotoLocationsRepository $photoLocationsRepository)
    {
        $this->photoLocationsRepository = $photoLocationsRepository;
    }

    public function count()
    {
        return $this->photoLocationsRepository->count([]);
    }

    public function maxId(): int
    {
        $max_id = 0;
        $all_locations = $this->photoLocationsRepository->findAll();

        foreach ($all_locations as $location) {
            if ((int) $location->getId() > $max_id) {
                $max_id = (int) $location->getId();
            }
        }
        return $max_id;
//        return $this->photoLocationsRepository->findOneBy(['id' => $max_id])->getLocation();
    }

}

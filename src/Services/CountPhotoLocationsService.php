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

    public function photoLocation(CountPhotoLocationsService $countPhotoLocationsService)
    {
        if ($countPhotoLocationsService->count() == 1) {
            $result = $this->photoLocationsRepository->countPhotoLocations();
            return $result;
        }

        return null; // Return null or handle the case when count is not 1
    }

}

<?php


namespace App\Services;

use App\Repository\PhotosRepository;

class CountPhotos
{
    public function calculateTotalPhotos($location)
    {
        $photos = $this->photosRepository->findBy([
            'location' => $location
        ]);
        if ($photos) {
            return count($photos);
        }
        return 0;

    }


    public function __construct(PhotosRepository $photosRepository)
    {
        $this->photosRepository = $photosRepository;
    }
}
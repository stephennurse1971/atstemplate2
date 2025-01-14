<?php


namespace App\Services;

use App\Repository\PhotosRepository;

class PhotoAuthorsByLocation
{
    public function authorList($location)
    {
        $photos = $this->photosRepository->findByLocation($location);
        $author_list = [];
        foreach ($photos as $photo) {
            $author_list[] = $photo->getUploadedBy();
        }
       $unique_authors_list = [];
        foreach($author_list as $item){
            if(!in_array($item,$unique_authors_list)){
                $unique_authors_list[]=$item;
            }
        }
        usort($unique_authors_list, function ($first, $second) {
            return strcmp($first->getFullName(), $second->getFullName());
        });
        return $unique_authors_list;
    }

    public function __construct(PhotosRepository $photosRepository)
    {
        $this->photosRepository = $photosRepository;
    }
}

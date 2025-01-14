<?php

namespace App\Entity;

use App\Repository\PhotosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhotosRepository::class)
 */
class Photos
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoFile;

    /**
     * @ORM\ManyToOne(targetEntity=PhotoLocations::class, inversedBy="Photos")
     */
    private $location;



    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $uploadedBy;


    /**
     * @ORM\ManyToMany(targetEntity=User::class)
     */
    private $favourites;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rotate;



        public function __construct()
    {
      //  $this->person = new ArrayCollection();
      $this->favourites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhotoFile(): ?string
    {
        return $this->photoFile;
    }

    public function setPhotoFile(?string $photoFile): self
    {
        $this->photoFile = $photoFile;

        return $this;
    }

    public function getLocation(): ?PhotoLocations
    {
        return $this->location;
    }

    public function setLocation(?PhotoLocations $location): self
    {
        $this->location = $location;

        return $this;
    }


    public function getUploadedBy(): ?User
    {
        return $this->uploadedBy;
    }

    public function setUploadedBy(?User $uploadedBy): self
    {
        $this->uploadedBy = $uploadedBy;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFavourites(): Collection
    {
        return $this->favourites;
    }

    public function addFavourite(User $favourite): self
    {
        if (!$this->favourites->contains($favourite)) {
            $this->favourites[] = $favourite;
        }

        return $this;
    }

    public function removeFavourite(User $favourite): self
    {
        $this->favourites->removeElement($favourite);

        return $this;
    }

    public function getRotate(): ?int
    {
        return $this->rotate;
    }

    public function setRotate(?int $rotate): self
    {
        $this->rotate = $rotate;

        return $this;
    }





}

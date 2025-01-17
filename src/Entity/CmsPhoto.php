<?php

namespace App\Entity;

use App\Repository\CmsPhotoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CmsPhotoRepository::class)
 */
class CmsPhoto
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
    private $photo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;



    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photoOrVideo;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class)
     */
    private $product;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $staticPageName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ranking;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rotate;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }



    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getPhotoOrVideo(): ?string
    {
        return $this->photoOrVideo;
    }

    public function setPhotoOrVideo(?string $photoOrVideo): self
    {
        $this->photoOrVideo = $photoOrVideo;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getStaticPageName(): ?string
    {
        return $this->staticPageName;
    }

    public function setStaticPageName(?string $staticPageName): self
    {
        $this->staticPageName = $staticPageName;

        return $this;
    }

    public function getRanking(): ?int
    {
        return $this->ranking;
    }

    public function setRanking(?int $ranking): self
    {
        $this->ranking = $ranking;

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

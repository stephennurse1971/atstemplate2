<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: "product")]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $product = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $ranking = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $isActive = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $comments = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $includeInFooter = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(?string $product): self
    {
        $this->product = $product;
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

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;
        return $this;
    }

    public function getIncludeInFooter(): ?bool
    {
        return $this->includeInFooter;
    }

    public function setIncludeInFooter(?bool $includeInFooter): self
    {
        $this->includeInFooter = $includeInFooter;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;
        return $this;
    }
}

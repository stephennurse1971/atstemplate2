<?php

namespace App\Entity;

use App\Repository\BusinessTypesRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\BusinessContacts;

#[ORM\Entity(repositoryClass: BusinessTypesRepository::class)]
#[ORM\Table(name: "business_types")]
class BusinessTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $businessType = null;

    #[ORM\Column(type: "float", nullable: true)]
    private ?float $ranking = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: BusinessContacts::class, mappedBy: "businessType")]
    private Collection $businessContacts;

    #[ORM\ManyToOne(targetEntity: MapIcons::class)]
    private ?MapIcons $mapIcon = null;

    public function __construct()
    {
        $this->businessContacts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBusinessType(): ?string
    {
        return $this->businessType;
    }

    public function setBusinessType(?string $businessType): self
    {
        $this->businessType = $businessType;

        return $this;
    }

    public function getRanking(): ?float
    {
        return $this->ranking;
    }

    public function setRanking(?float $ranking): self
    {
        $this->ranking = $ranking;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, BusinessContacts>
     */
    public function getBusinessContacts(): Collection
    {
        return $this->businessContacts;
    }

    public function getMapIcon(): ?MapIcons
    {
        return $this->mapIcon;
    }

    public function setMapIcon(?MapIcons $mapIcon): self
    {
        $this->mapIcon = $mapIcon;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\BusinessTypesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BusinessTypesRepository::class)
 */
class BusinessTypes
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
    private $businessType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mapIcon;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mapIconColour;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mapDisplay;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ranking;




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

    public function getMapIcon(): ?string
    {
        return $this->mapIcon;
    }

    public function setMapIcon(?string $mapIcon): self
    {
        $this->mapIcon = $mapIcon;

        return $this;
    }

    public function getMapIconColour(): ?string
    {
        return $this->mapIconColour;
    }

    public function setMapIconColour(?string $mapIconColour): self
    {
        $this->mapIconColour = $mapIconColour;

        return $this;
    }

    public function getMapDisplay(): ?int
    {
        return $this->mapDisplay;
    }

    public function setMapDisplay(?int $mapDisplay): self
    {
        $this->mapDisplay = $mapDisplay;

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

}

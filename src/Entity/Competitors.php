<?php

namespace App\Entity;

use App\Repository\CompetitorsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompetitorsRepository::class)]
#[ORM\Table(name: "Competitors")]


class Competitors
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $name;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $webSite;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $telephone;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $facebook;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $linkedIn;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $instagram;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $twitter;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $type;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $competitorAddressCity;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $competitorAddressPostalCode;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $competitorAddressCountry;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $competitorAddressLongitude;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $competitorAddressLatitude;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $competitorAddressStreet;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWebSite(): ?string
    {
        return $this->webSite;
    }

    public function setWebSite(?string $webSite): self
    {
        $this->webSite = $webSite;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getLinkedIn(): ?string
    {
        return $this->linkedIn;
    }

    public function setLinkedIn(?string $linkedIn): self
    {
        $this->linkedIn = $linkedIn;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCompetitorAddressCity(): ?string
    {
        return $this->competitorAddressCity;
    }

    public function setCompetitorAddressCity(?string $competitorAddressCity): self
    {
        $this->competitorAddressCity = $competitorAddressCity;

        return $this;
    }

    public function getCompetitorAddressPostalCode(): ?string
    {
        return $this->competitorAddressPostalCode;
    }

    public function setCompetitorAddressPostalCode(string $competitorAddressPostalCode): self
    {
        $this->competitorAddressPostalCode = $competitorAddressPostalCode;

        return $this;
    }

    public function getCompetitorAddressCountry(): ?string
    {
        return $this->competitorAddressCountry;
    }

    public function setCompetitorAddressCountry(?string $competitorAddressCountry): self
    {
        $this->competitorAddressCountry = $competitorAddressCountry;

        return $this;
    }

    public function getCompetitorAddressLongitude(): ?string
    {
        return $this->competitorAddressLongitude;
    }

    public function setCompetitorAddressLongitude(?string $competitorAddressLongitude): self
    {
        $this->competitorAddressLongitude = $competitorAddressLongitude;

        return $this;
    }

    public function getCompetitorAddressLatitude(): ?string
    {
        return $this->competitorAddressLatitude;
    }

    public function setCompetitorAddressLatitude(?string $competitorAddressLatitude): self
    {
        $this->competitorAddressLatitude = $competitorAddressLatitude;

        return $this;
    }

    public function getCompetitorAddressStreet(): ?string
    {
        return $this->competitorAddressStreet;
    }

    public function setCompetitorAddressStreet(?string $competitorAddressStreet): self
    {
        $this->competitorAddressStreet = $competitorAddressStreet;

        return $this;
    }
}

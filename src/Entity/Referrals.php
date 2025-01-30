<?php

namespace App\Entity;

use App\Repository\BusinessContactsRepository;
use App\Repository\ReferralsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReferralsRepository::class)]
#[ORM\Table(name: "referrals")]
class Referrals
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: BusinessContacts::class)]
    private ?BusinessContacts $businessSite = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $action = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateTime = null;

    #[ORM\ManyToOne(targetEntity: BusinessContacts::class, inversedBy: 'referrals')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?BusinessContacts $businessContact = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBusinessSite(): ?BusinessContacts
    {
        return $this->businessSite;
    }

    public function setBusinessSite(?BusinessContacts $businessSite): self
    {
        $this->businessSite = $businessSite;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(?\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getBusinessContact(): ?BusinessContacts
    {
        return $this->businessContact;
    }

    public function setBusinessContact(?BusinessContacts $businessContact): self
    {
        $this->businessContact = $businessContact;

        return $this;
    }
}

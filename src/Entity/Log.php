<?php

namespace App\Entity;

use App\Entity\User;
use App\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\Table(name: 'log')]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $eventKey;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'logs')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $additionalInfo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getEventKey(): ?string
    {
        return $this->eventKey;
    }

    public function setEventKey(?string $eventKey): self
    {
        $this->eventKey = $eventKey;
        return $this;
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

    public function getAdditionalInfo(): ?string
    {
        return $this->additionalInfo;
    }

    public function setAdditionalInfo(?string $additionalInfo): self
    {
        $this->additionalInfo = $additionalInfo;
        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\FileAttachmentsRepository;
use App\Repository\UsefulLinksRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: FileAttachmentsRepository::class)]
#[ORM\Table(name: "file_attachments")]


class FileAttachments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "date", nullable: true)]
    private $date;

    #[ORM\Column(type: "json", nullable: true)]
    private $attachments = [];

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $description;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $category;

    #[ORM\Column(type: "text", nullable: true)]
    private $notes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAttachments(): ?array
    {
        return $this->attachments;
    }

    public function setAttachments(?array $attachments): self
    {
        $this->attachments = $attachments;

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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }
}

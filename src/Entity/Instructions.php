<?php

namespace App\Entity;

use App\Repository\InstructionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstructionsRepository::class)]
#[ORM\Table(name: "instructions")]

class Instructions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $topic;

    #[ORM\Column(type: "text", nullable: true)]
    private $summary;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $media;

    #[ORM\Column(type: "string", length: 255)]
    private $photoOrVideo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(?string $topic): self
    {
        $this->topic = $topic;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(?string $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getPhotoOrVideo(): ?string
    {
        return $this->photoOrVideo;
    }

    public function setPhotoOrVideo(string $photoOrVideo): self
    {
        $this->photoOrVideo = $photoOrVideo;

        return $this;
    }
}

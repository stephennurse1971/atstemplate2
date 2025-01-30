<?php

namespace App\Entity;

use App\Repository\CmsCopyPageFormatsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CmsCopyPageFormatsRepository::class)
 */
#[ORM\Entity(repositoryClass: CmsCopyPageFormatsRepository::class)]
#[ORM\Table(name: "cms_copy_page_formats")]


class CmsCopyPageFormats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $name;

    #[ORM\Column(type: "text", nullable: true)]
    private $description;

    #[ORM\Column(type: "text", nullable: true)]
    private $pros;

    #[ORM\Column(type: "text", nullable: true)]
    private $cons;

    #[ORM\Column(type: "text", nullable: true)]
    private $code;

    #[ORM\Column(type: "text", nullable: true)]
    private $uses;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPros(): ?string
    {
        return $this->pros;
    }

    public function setPros(?string $pros): self
    {
        $this->pros = $pros;

        return $this;
    }

    public function getCons(): ?string
    {
        return $this->cons;
    }

    public function setCons(?string $cons): self
    {
        $this->cons = $cons;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUses(): ?string
    {
        return $this->uses;
    }

    public function setUses(?string $uses): self
    {
        $this->uses = $uses;

        return $this;
    }
}

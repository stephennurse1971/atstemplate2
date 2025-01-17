<?php

namespace App\Entity;

use App\Repository\MapIconsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MapIconsRepository::class)
 */
class MapIcons
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $iconFile;

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

    public function getIconFile(): ?string
    {
        return $this->iconFile;
    }

    public function setIconFile(?string $iconFile): self
    {
        $this->iconFile = $iconFile;

        return $this;
    }
}

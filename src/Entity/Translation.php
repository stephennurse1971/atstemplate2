<?php

namespace App\Entity;

use App\Repository\TranslationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TranslationRepository::class)
 */
class Translation
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
    private $english;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $french;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $german;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $spanish;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $russian;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $entity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEnglish(): ?string
    {
        return $this->english;
    }

    public function setEnglish(?string $english): self
    {
        $this->english = $english;

        return $this;
    }

    public function getFrench(): ?string
    {
        return $this->french;
    }

    public function setFrench(?string $french): self
    {
        $this->french = $french;

        return $this;
    }

    public function getGerman(): ?string
    {
        return $this->german;
    }

    public function setGerman(?string $german): self
    {
        $this->german = $german;

        return $this;
    }

    public function getSpanish(): ?string
    {
        return $this->spanish;
    }

    public function setSpanish(string $spanish): self
    {
        $this->spanish = $spanish;

        return $this;
    }

    public function getRussian(): ?string
    {
        return $this->russian;
    }

    public function setRussian(?string $russian): self
    {
        $this->russian = $russian;

        return $this;
    }

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(?string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\LanguagesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LanguagesRepository::class)]
#[ORM\Table(name: "languages")]
class Languages
{
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column(type: "integer")]
private ?int $id = null;

#[ORM\Column(type: "string", length: 255, nullable: true)]
private ?string $language = null;

#[ORM\Column(type: "string", length: 255, nullable: true)]
private ?string $abbreviation = null;

#[ORM\Column(type: "string", length: 255, nullable: true)]
private ?string $icon = null;

#[ORM\Column(type: "boolean", nullable: true)]
private ?bool $isActive = null;

#[ORM\Column(type: "boolean", nullable: true)]
private ?bool $linkedInOther = null;

#[ORM\Column(type: "integer", nullable: true)]
private ?int $ranking = null;

public function getId(): ?int
{
return $this->id;
}

public function getLanguage(): ?string
{
return $this->language;
}

public function setLanguage(?string $language): self
{
$this->language = $language;

return $this;
}

public function getAbbreviation(): ?string
{
return $this->abbreviation;
}

public function setAbbreviation(?string $abbreviation): self
{
$this->abbreviation = $abbreviation;

return $this;
}

public function getIcon(): ?string
{
return $this->icon;
}

public function setIcon(?string $icon): self
{
$this->icon = $icon;

return $this;
}

public function isIsActive(): ?bool
{
return $this->isActive;
}

public function setIsActive(?bool $isActive): self
{
$this->isActive = $isActive;

return $this;
}

public function getLinkedInOther(): ?bool
{
return $this->linkedInOther;
}

public function setLinkedInOther(?bool $linkedInOther): self
{
$this->linkedInOther = $linkedInOther;

return $this;
}

public function getRanking(): ?int
{
return $this->ranking;
}

public function setRanking(?int $ranking): self
{
$this->ranking = $ranking;

return $this;
}
}

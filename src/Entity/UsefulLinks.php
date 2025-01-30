<?php

namespace App\Entity;

use App\Repository\BusinessContactsRepository;
use App\Repository\UsefulLinksRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsefulLinksRepository::class)]
#[ORM\Table(name: "useful_links")]
class UsefulLinks
{
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column(type: "integer")]
private $id;

#[ORM\Column(type: "string", length: 255, nullable: true)]
private $category;

#[ORM\Column(type: "string", length: 255, nullable: true)]
private $name;

#[ORM\Column(type: "string", length: 255, nullable: true)]
private $link;

#[ORM\Column(type: "string", length: 255, nullable: true)]
private $login;

#[ORM\Column(type: "string", length: 255, nullable: true)]
private $password;

#[ORM\Column(type: "string", length: 255, nullable: true)]
private $notes;

#[ORM\Column(type: "boolean", nullable: true)]
private $private;

public function getId(): ?int
{
return $this->id;
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

public function getName(): ?string
{
return $this->name;
}

public function setName(?string $name): self
{
$this->name = $name;

return $this;
}

public function getLink(): ?string
{
return $this->link;
}

public function setLink(?string $link): self
{
$this->link = $link;

return $this;
}

public function getLogin(): ?string
{
return $this->login;
}

public function setLogin(?string $login): self
{
$this->login = $login;

return $this;
}

public function getPassword(): ?string
{
return $this->password;
}

public function setPassword(?string $password): self
{
$this->password = $password;

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

public function getPrivate(): ?bool
{
return $this->private;
}

public function setPrivate(?bool $private): self
{
$this->private = $private;

return $this;
}
}

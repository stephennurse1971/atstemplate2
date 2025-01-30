<?php

namespace App\Entity;

use App\Repository\FacebookGroupsReviewsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FacebookGroupsReviewsRepository::class)
 * @ORM\Table(name="facebook_groups_reviews")
 */
class FacebookGroupsReviews
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: FacebookGroups::class)]
    private $facebookGroup;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $date;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $reviewer;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFacebookGroup(): ?FacebookGroups
    {
        return $this->facebookGroup;
    }

    public function setFacebookGroup(?FacebookGroups $facebookGroup): self
    {
        $this->facebookGroup = $facebookGroup;

        return $this;
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

    public function getReviewer(): ?User
    {
        return $this->reviewer;
    }

    public function setReviewer(?User $reviewer): self
    {
        $this->reviewer = $reviewer;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}

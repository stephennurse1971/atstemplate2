<?php

namespace App\Entity;

use App\Repository\WeatherRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeatherRepository::class)]
#[ORM\Table(name: "weather")]


class Weather
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $date;

    #[ORM\Column(type: "integer", nullable: true)]
    private $time;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $weather;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $location;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $rain;

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

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(?int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getWeather(): ?string
    {
        return $this->weather;
    }

    public function setWeather(?string $weather): self
    {
        $this->weather = $weather;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getRain(): ?string
    {
        return $this->rain;
    }

    public function setRain(?string $rain): self
    {
        $this->rain = $rain;

        return $this;
    }
}

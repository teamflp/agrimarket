<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PlanRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity(repositoryClass: PlanRepository::class)]
#[ApiResource(
    operations:[
        new GetCollection(),
        new Get(),
        new POST(),
        new Put(),
        new Delete(),
    ]
)]
class Plan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column]
    private ?int $maxProducts = null;

    #[ORM\Column]
    private array $benefits = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getMaxProducts(): ?int
    {
        return $this->maxProducts;
    }

    public function setMaxProducts(int $maxProducts): static
    {
        $this->maxProducts = $maxProducts;

        return $this;
    }

    public function getBenefits(): array
    {
        return $this->benefits;
    }

    public function setBenefits(array $benefits): static
    {
        $this->benefits = $benefits;

        return $this;
    }
}

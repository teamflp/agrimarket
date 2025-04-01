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
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlanRepository::class)]

#[ApiResource(
    normalizationContext: ['groups' => ['plan:read']],
    denormalizationContext: ['groups' => ['plan:write']],
    operations:[
        new GetCollection(security: "is_granted('ROLE_USER')"),
        // GET /api/addresses (Tous les utilisateurs connectés)
        new Get(security: "is_granted('ROLE_USER')"),
         // GET /api/addresses/{id} (Tous les utilisateurs connectés)
        new POST(securityPostDenormalize: "is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"),
        // POST /api/addresses (Admin ou Editeur)
        new Put(securityPostDenormalize: "is_granted('ROLE_ADMIN') or (object.owner == user and is_granted('ROLE_EDITOR'))"), 
        // PUT /api/addresses/{id} (Admin ou Editeur propriétaire)
        new Delete(security: "is_granted('ROLE_ADMIN')"), 
        // DELETE /api/addresses/{id} (Admin seulement)
    ]
)]
class Plan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:read', 'product:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['product:read', 'product:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['product:read', 'product:write'])]
    private ?float $price = null;

    #[ORM\Column]
    #[Groups(['product:read', 'product:write'])]
    private ?int $duration = null;

    #[ORM\Column]
    #[Groups(['product:read', 'product:write'])]
    private ?int $maxProducts = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['product:read', 'product:write'])]
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

    public function getDurationInDays(): string
{
    return $this->duration . ' jours';
}
    public function setDurationInDays(): string
{
    return $this->duration . ' jours';
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

    // Transformation pour EasyAdmin : conversion en JSON
    public function getBenefitsAsJson(): string
    {
        return json_encode($this->benefits, JSON_PRETTY_PRINT);
    }

    public function setBenefitsAsJson(string $json): self
    {
        $decoded = json_decode($json, true);
        $this->benefits = is_array($decoded) ? $decoded : [];
        return $this;
    }
}

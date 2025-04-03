<?php

namespace App\Entity;

use App\Entity\User;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\SubscriptionRepository;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]

#[ApiResource(
    operations:[
        new GetCollection(),
        new Get(),
        new POST(),
        new Put(),
        new Delete(),
    ]
)]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $plan = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $starDateAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endDateAt = null;

    // Relation avec User (un user peut avoir plusieurs subscriptions)
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(nullable: false)] // Assurez-vous que cette colonne ne peut pas être nulle
    private ?User $utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(string $plan): static
    {
        $this->plan = $plan;

        return $this;
    }

    public function getStarDateAt(): ?\DateTimeImmutable
    {
        return $this->starDateAt;
    }

    public function setStarDateAt(\DateTimeImmutable $starDateAt): static
    {
        $this->starDateAt = $starDateAt;

        return $this;
    }

    public function getEndDateAt(): ?\DateTimeImmutable
    {
        return $this->endDateAt;
    }

    public function setEndDateAt(\DateTimeImmutable $endDateAt): static
    {
        $this->endDateAt = $endDateAt;

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}

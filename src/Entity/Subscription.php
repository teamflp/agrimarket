<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
#[ApiResource(
    operations: [

       new getCollection(), // GET /api/subscriptions

        new get(),          // GET /api/subscriptions/{id}

        new post()          // POST /api/subscriptions

    ],
    normalizationContext: ['groups' => ['subscription:read']],
    denormalizationContext: ['groups' => ['subscription:write']]
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
    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
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

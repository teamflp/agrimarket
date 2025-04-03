<?php

namespace App\Entity;

use App\Entity\User;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\SubscriptionRepository;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]

#[ApiResource(
    operations:[
        new GetCollection(),
        new Get(),
        new POST(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),
        new Put(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),
        new Delete(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),
    ]
)]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
    private ?string $plan = null;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?\DateTime $starDateAt = null;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?\DateTime $endDateAt = null;

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

    public function getStarDateAt(): ?\DateTime
    {
        return $this->starDateAt;
    }

    public function setStarDateAt(\DateTime $starDateAt): static
    {
        $this->starDateAt = $starDateAt;

        return $this;
    }

    public function getEndDateAt(): ?\DateTime
    {
        return $this->endDateAt;
    }

    public function setEndDateAt(\DateTime $endDateAt): static
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

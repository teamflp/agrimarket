<?php

namespace App\Entity;

use Assert\Range;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RatingRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RatingRepository::class)]

#[ApiResource(
    normalizationContext: ['groups' => ['rating:read']],
    denormalizationContext: ['groups' => ['rating:write']],

    operations:[
        new GetCollection(),
        new Get(),
        new POST(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),
        new Put(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),
        new Delete(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),
    ]
)]
class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 0,
        max: 5,
        notInRangeMessage: 'La note doit être comprise entre {{ min }} et {{ max }}.',
        groups: ['read', 'write', 'easyadmin']
    )]
    #[Groups(['read', 'write'])]
    private ?int $score = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read', 'write'])]
    private ?string $comment = null;

    #[ORM\Column]
    #[Groups(['read'])]
    private ?\DateTimeImmutable $createdAt = null;

    // L’utilisateur qui a laissé la note
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ratings')]

    private ?User  $buyer = null; // Assurez-vous que c'est bien 'buyer' et non 'user'


    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'ratings')]

    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score . ' /5';

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): static
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

}

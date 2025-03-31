<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CouponRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['address:read']],
    denormalizationContext: ['groups' => ['address:write']],
    //operations: [
        //new Get(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)")
    //]
    operations:[
        new GetCollection(),// GET /api/coupons
        new Get(),// GET /api/coupons/{id}
        new POST(),// POST /api/coupons
        new Put(),// PUT /api/coupons/{id}
        new Delete(),// DELETE /api/coupons/{id}
    ]
)]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ['percentage', 'fixed'], message: 'Choose a valid discount type: percentage or fixed.')]
    private ?string $discountType = null;

    #[ORM\Column]
    private ?float $value = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $expirationDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $usageLimit = null;

    #[ORM\Column]
    private ?int $usedCount = null;

    public function __construct()
    {
        // Initialiser "usedCount" à zéro
        $this->usedCount = 0;
    }

    // Prévoir une relation ManyToOne vers User (ou Order) au cas où
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'coupons')]
    #[ORM\JoinColumn(nullable: true)] // Rend la relation facultative
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'coupons')]
#[ORM\JoinColumn(nullable: true)]
private ?User $order = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getDiscountType(): ?string
    {
        return $this->discountType;
    }

    public function setDiscountType(string $discountType): static
    {
        $this->discountType = $discountType;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTimeInterface $expirationDate): static
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getUsageLimit(): ?int
    {
        return $this->usageLimit;
    }

    public function setUsageLimit(?int $usageLimit): static
    {
        $this->usageLimit = $usageLimit;

        return $this;
    }

    public function getUsedCount(): ?int
    {
        return $this->usedCount;
    }

    public function setUsedCount(int $usedCount): static
    {
        $this->usedCount = $usedCount;

        return $this;
    }

    // Getter/Setter pour la relation avec User
    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    // Getter/Setter pour la relation avec User
    public function getOrder(): ?Order
    {
        return $this->order;
    }
    
    public function setOrder(?Order $order): static
    {
        $this->order = $order;
        return $this;
    }
}

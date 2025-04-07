<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),    // GET /api/orders
        new Get(),              // GET /api/orders/{id}
        new POST(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),             // POST /api/orders
        new Put(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),              // PUT /api/orders/{id}
        new Delete(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),           // DELETE /api/orders/{id}
    ],
)]

class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['order:read'])]
    private ?int $id = null;

    // Relation avec l’utilisateur acheteur
    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[Groups(['order:read', 'order:write'])]
    private ?User $buyer = null;

    #[ORM\Column(length: 255)]
    #[Groups(['order:read', 'order:write'])]
    #[Assert\Choice(choices: ['pending', 'shipped', 'delivered', 'canceled'], message: 'Statut invalide')]
    private ?string $status = null; // ex: pending, shipped, delivered, canceled...

    #[ORM\Column]
    #[Groups(['order:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    // Montant total (peut être calculé dynamiquement ou stocké)
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    #[Groups(['order:read', 'order:write'])]
    private ?string $total = null;

    /**
     * @var Collection<int, OrderItem>
     *
     * Relation avec OrderItem
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'orders')]
    #[Groups(['order:read'])]
    private Collection $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrders($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrders() === $this) {
                $orderItem->setOrders(null);
            }
        }

        return $this;
    }
}

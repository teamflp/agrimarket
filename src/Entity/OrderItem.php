<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\OrderItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),    // GET /api/order-items
        new Get(),              // GET /api/order-items/{id}
        new POST(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),             // POST /api/order-items
        new Put(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),              // PUT /api/order-items/{id}
        new Delete(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),           // DELETE /api/order-items/{id}
    ],
)]

class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['order_item:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[Groups(['order_item:read', 'order_item:write'])]
    private ?Order $orders = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[Groups(['order_item:read', 'order_item:write'])]
    private ?Product $product = null;

    #[ORM\Column]
    #[Groups(['order_item:read', 'order_item:write'])]
    #[Assert\Positive(message: "La quantité doit être positive")]
    private ?int $quantity = null;

    // On peut stocker le prix unitaire au moment de la commande
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['order_item:read', 'internal:write'])]
    private ?string $unitPrice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrders(): ?Order
    {
        return $this->orders;
    }

    public function setOrders(?Order $orders): static
    {
        $this->orders = $orders;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        if ($product !== null) {
            $this->setUnitPrice($product->getPrice());
        }

        return $this;
    }


    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }
}

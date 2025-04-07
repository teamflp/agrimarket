<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\RefundRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RefundRequestRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),    // GET /api/refund-requests
        new Get(),              // GET /api/refund-requests/{id}
        new POST(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),             // POST /api/refund-requests
        new Put(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),              // PUT /api/refund-requests/{id}
        new Delete(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),           // DELETE /api/refund-requests/{id}
    ],
)]

class RefundRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['refund_request:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['refund_request:read', 'refund_request:write'])]
    #[Assert\NotBlank(message: "La raison est requise")]
    private ?string $reason = null;

    #[ORM\Column(length: 255)]
    #[Groups(['refund_request:read', 'refund_request:write'])]
    #[Assert\Choice(choices: ['pending', 'approved', 'rejected'], message: 'Statut invalide')]
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['refund_request:read', 'refund_request:write'])]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['refund_request:read'])]
    private ?\DateTimeInterface $createAt = null;

    #[ORM\ManyToOne(inversedBy: 'refundRequests')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['refund_request:read', 'refund_request:write'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}

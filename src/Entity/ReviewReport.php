<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\ReviewReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewReportRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),    // GET /api/review-reports
        new Get(),              // GET /api/review-reports/{id}
        new POST(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),             // POST /api/review-reports
        new Put(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),              // PUT /api/review-reports
        new Delete(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),           // DELETE /api/review-reports/{id}
    ],
)]

class ReviewReport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['review_report:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['review_report:read', 'review_report:write'])]
    #[Assert\NotBlank(message: "La raison est requise")]
    private ?string $reason = null;

    #[ORM\Column(length: 255)]
    #[Groups(['review_report:read', 'review_report:write'])]
    #[Assert\Choice(choices: ['pending', 'validated', 'rejected'], message: 'Statut invalide')]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['review_report:read'])]
    private ?\DateTimeInterface $createAt = null;

    #[ORM\ManyToOne(inversedBy: 'reviewReports')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['review_report:read', 'review_report:write'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reviewReports')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['review_report:read', 'review_report:write'])]
    private ?Rating $rating = null;

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

    public function getRating(): ?Rating
    {
        return $this->rating;
    }

    public function setRating(?Rating $rating): static
    {
        $this->rating = $rating;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatingRepository::class)]
class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // L’utilisateur qui a laissé la note
    #[ORM\ManyToOne(inversedBy: 'ratings')]
    private ?User $buyer = null;

    #[ORM\ManyToOne(inversedBy: 'ratings')]
    private ?Product $product = null;

    /**
     * @var Collection<int, ReviewReport>
     */
    #[ORM\OneToMany(targetEntity: ReviewReport::class, mappedBy: 'rating')]
    private Collection $reviewReports;

    public function __construct()
    {
        $this->reviewReports = new ArrayCollection();
    }

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
        $this->score = $score;

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

    /**
     * @return Collection<int, ReviewReport>
     */
    public function getReviewReports(): Collection
    {
        return $this->reviewReports;
    }

    public function addReviewReport(ReviewReport $reviewReport): static
    {
        if (!$this->reviewReports->contains($reviewReport)) {
            $this->reviewReports->add($reviewReport);
            $reviewReport->setRating($this);
        }

        return $this;
    }

    public function removeReviewReport(ReviewReport $reviewReport): static
    {
        if ($this->reviewReports->removeElement($reviewReport)) {
            // set the owning side to null (unless already changed)
            if ($reviewReport->getRating() === $this) {
                $reviewReport->setRating(null);
            }
        }

        return $this;
    }
}

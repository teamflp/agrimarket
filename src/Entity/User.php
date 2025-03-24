<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

// Représente les informations de base d'un utilisateur, qui peut être un agriculteur (vendeur), un acheteur , ou un administrateur .
//Nous utilisons un champ roles(array) pour distinguer les différents types d'utilisateurs.
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $phoneNumber = null;

    /**
     * @var Collection<int, Subscription>
     *
     * Relation avec Subscription (un utilisateur peut avoir plusieurs abonnements dans le temps)
     */
    #[ORM\OneToMany(targetEntity: Subscription::class, mappedBy: 'utilisateur')]
    private Collection $subscriptions;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'farmer')]
    private Collection $products;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'buyer')]
    private Collection $orders;

    /**
     * @var Collection<int, Rating>
     */
    #[ORM\OneToMany(targetEntity: Rating::class, mappedBy: 'buyer')]
    private Collection $ratings;

    /**
     * @var Collection<int, RefundRequest>
     */
    #[ORM\OneToMany(targetEntity: RefundRequest::class, mappedBy: 'user')]
    private Collection $refundRequests;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user')]
    private Collection $notifications;

    /**
     * @var Collection<int, ReviewReport>
     */
    #[ORM\OneToMany(targetEntity: ReviewReport::class, mappedBy: 'user')]
    private Collection $reviewReports;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->refundRequests = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->reviewReports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): static
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setUtilisateur($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): static
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getUtilisateur() === $this) {
                $subscription->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setFarmer($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getFarmer() === $this) {
                $product->setFarmer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setBuyer($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getBuyer() === $this) {
                $order->setBuyer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setBuyer($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getBuyer() === $this) {
                $rating->setBuyer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RefundRequest>
     */
    public function getRefundRequests(): Collection
    {
        return $this->refundRequests;
    }

    public function addRefundRequest(RefundRequest $refundRequest): static
    {
        if (!$this->refundRequests->contains($refundRequest)) {
            $this->refundRequests->add($refundRequest);
            $refundRequest->setUser($this);
        }

        return $this;
    }

    public function removeRefundRequest(RefundRequest $refundRequest): static
    {
        if ($this->refundRequests->removeElement($refundRequest)) {
            // set the owning side to null (unless already changed)
            if ($refundRequest->getUser() === $this) {
                $refundRequest->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

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
            $reviewReport->setUser($this);
        }

        return $this;
    }

    public function removeReviewReport(ReviewReport $reviewReport): static
    {
        if ($this->reviewReports->removeElement($reviewReport)) {
            // set the owning side to null (unless already changed)
            if ($reviewReport->getUser() === $this) {
                $reviewReport->setUser(null);
            }
        }

        return $this;
    }
}

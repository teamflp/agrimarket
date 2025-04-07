<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),    // GET /api/notifications
        new Get(),              // GET /api/notifications/{id}
        new POST(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),             // POST /api/notifications
        new Put(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER'))"),              // PUT /api/notifications
        new Delete(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),           // DELETE /api/notifications/{id}
    ]
)]

class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notification:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['notification:read', 'notification:write'])]
    #[Assert\NotBlank(message: "Le titre est requis")]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['notification:read', 'notification:write'])]
    #[Assert\NotBlank(message: "Le contenu est requis")]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Groups(['notification:read', 'notification:write'])]
    #[Assert\Choice(choices: ['email', 'sms', 'push'], message: 'Canal invalide')]
    private ?string $channel = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['notification:read'])]
    private ?\DateTimeInterface $createAt = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notification:read', 'notification:write'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): static
    {
        $this->channel = $channel;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(?\DateTimeInterface $createAt): static
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

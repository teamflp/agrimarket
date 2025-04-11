<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\AddressRepository;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new POST(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER')"),
        new Put(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER'))"),
        new Delete(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_FARMER'))"),
    ],
    normalizationContext: ['groups' => ['address:read']],
    denormalizationContext: ['groups' => ['address:write']],
    paginationItemsPerPage: 10,
)]
#[ApiFilter(SearchFilter::class, properties: ['city' => 'partial', 'zipCode' => 'partial'])]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['address:read', 'address:write'])]
    #[NotBlank (message: 'Veuillez renseigner le champ rue')]
    private ?string $street = null;

    #[ORM\Column(length: 255)]
    #[Groups(['address:read', 'address:write'])]
    #[NotBlank (message: 'Veuillez renseigner le champ ville')]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Groups(['address:read', 'address:write'])]
    #[NotBlank (message: 'Veuillez renseigner le champ code postal')]
    #[Regex(
        pattern: '/^\d{5}$/',
        message: 'Le code postal doit contenir 5 chiffres.',
    )]
    private ?string $zipCode = null;

    #[ORM\Column(length: 255)]
    #[Groups(['address:read', 'address:write'])]
    #[NotBlank (message: 'Veuillez renseigner le champ pays')]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['address:read', 'address:write'])]
    private ?string $labe = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['address:read', 'address:write'])]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['address:read', 'address:write'])]
    private ?float $longitude = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['address:read', 'address:write'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getLabe(): ?string
    {
        return $this->labe;
    }

    public function setLabe(?string $labe): static
    {
        $this->labe = $labe;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

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
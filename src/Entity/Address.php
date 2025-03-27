<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\AddressRepository;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AddressRepository::class)]

#[ApiResource(
    normalizationContext: ['groups' => ['address:read']],
    denormalizationContext: ['groups' => ['address:write']],
    //operations: [
        //new Get(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)")
    //]
    operations:[
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),// GET/api/addresses
        new Get(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.user == user)"), // GET /api/addresses/{id}
        new POST(security: "is_granted('ROLE_ADMIN')"),// POST/api/addresses
        new Put(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.user == user)"), // PUT /api/addresses/{id}
        new Delete(security: "is_granted('ROLE_ADMIN')"), // DELETE /api/addresses/{id}
    ]
)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['address:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['address:read', 'address:write'])]
    private ?string $street = null;

    #[ORM\Column(length: 255)]
    #[Groups(['address:read', 'address:write'])]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Groups(['address:read', 'address:write'])]
    private ?string $zipCode = null;

    #[ORM\Column(length: 255)]
    #[Groups(['address:read'])]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['address:read', 'address:write'])]
    private ?string $labe = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['address:read'])]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['address:read'])]
    private ?float $longitude = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'adresses')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addAdress($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeAdress($this);
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CategoryRepository;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;
//use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['address:read']],
    denormalizationContext: ['groups' => ['address:write']],
    //operations: [
        //new Get(security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getUser() == user)")
    //]
    operations:[
        new GetCollection(),// GET /api/categories
        new Get(), // GET /api/categories/{id}
        new POST(),// POST /api/categories
        new Put(),// PUT /api/categories/{id}
        new Delete(),// DELETE /api/categories/{id}
    ] 
)]
    
    //  On ajoute cette annotation pour exposer l'entité en tant que ressource API
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['category:read', 'category:write'])]
    private ?string $name = null;

    /**
     * @var Collection<int, Product>
     *
 * @Relation bidirectionnelle : une catégorie peut avoir plusieurs produits
     */

    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category')]
    // Ici, on n'ajoute pas forcément de groupe ici pour éviter de renvoyer des boucles de sérialisation
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }
}
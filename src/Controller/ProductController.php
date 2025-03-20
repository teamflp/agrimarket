<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api/products')]
final class ProductController extends AbstractController
{
    /**
     * GET /api/products
     * Liste tous les produits
     */
    #[Route('', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();

        return $this->json($products, 200, [], ['groups' => 'product:read']);
    }

    /**
     * GET /api/products/{id}
     * Récupère un produit par son ID
     */
    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(int $id, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw new NotFoundHttpException('Produit introuvable');
        }

        return $this->json($product, 200, [], ['groups' => 'product:read']);
    }

    /**
     * POST /api/products
     * Créer un nouveau produit
     * Body JSON attendu, ex:
     * {
     *    "name": "Orange",
     *    "slug": "orange",
     *    "description": "C'est un fruit",
     *    "price": "2",
     *    "quantity": 10,
     *    "category": 1,   <-- ID de la catégorie
     *    "farmer": 5,     <-- ID du user agriculteur
     *    "illustration": "orange.jpg"
     * }
     */
    #[Route('', name: 'app_product_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ProductRepository $productRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérifications de base
        if (empty($data['name']) || empty($data['slug']) || empty($data['description'])) {
            return $this->json(['error' => 'Certains champs obligatoires sont manquants.'], 400);
        }

        // Créer et remplir l'entité
        $product = new Product();
        $product->setName($data['name']);
        $product->setSlug($data['slug']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price'] ?? '0');
        $product->setQuantity($data['quantity'] ?? 0);
        $product->setIllustration($data['illustration'] ?? 'placeholder.jpg');

        // Récupérer la Category si un ID de catégorie est passé
        if (!empty($data['category'])) {
            $category = $em->getRepository(\App\Entity\Category::class)->find($data['category']);
            if ($category) {
                $product->setCategory($category);
            }
        }

        // Récupérer le Farmer (User) si un ID de user est passé (requis)
        if (empty($data['farmer'])) {
            return $this->json(['error' => 'Le champ "farmer" est requis.'], 400);
        }

        $farmer = $em->getRepository(User::class)->find($data['farmer']);
        if (!$farmer) {
            return $this->json(['error' => 'Farmer introuvable.'], 400);
        }
        $product->setFarmer($farmer);

        // Persister en base
        $em->persist($product);
        $em->flush();

        return $this->json($product, 201, [], ['groups' => 'product:read']);
    }

    /**
     * PUT /api/products/{id}
     * Met à jour un produit existant
     */
    #[Route('/{id}', name: 'app_product_update', methods: ['PUT'])]
    public function update(int $id, Request $request, ProductRepository $productRepository, EntityManagerInterface $em): JsonResponse
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw new NotFoundHttpException('Produit introuvable');
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        if (isset($data['slug'])) {
            $product->setSlug($data['slug']);
        }
        if (isset($data['description'])) {
            $product->setDescription($data['description']);
        }
        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }
        if (isset($data['quantity'])) {
            $product->setQuantity($data['quantity']);
        }
        if (isset($data['illustration'])) {
            $product->setIllustration($data['illustration']);
        }

        // Mise à jour éventuelle de la catégorie
        if (array_key_exists('category', $data)) {
            if (!empty($data['category'])) {
                $category = $em->getRepository(\App\Entity\Category::class)->find($data['category']);
                if ($category) {
                    $product->setCategory($category);
                }
            } else {
                // Si 'category' est null ou vide, on enlève la catégorie
                $product->setCategory(null);
            }
        }

        // Mise à jour éventuelle du Farmer
        if (array_key_exists('farmer', $data)) {
            if (!empty($data['farmer'])) {
                $farmer = $em->getRepository(\App\Entity\User::class)->find($data['farmer']);
                if (!$farmer) {
                    return $this->json(['error' => 'Farmer introuvable.'], 400);
                }
                $product->setFarmer($farmer);
            } else {
                // Champ farmer ne doit pas être null, car la relation est not nullable
                return $this->json(['error' => 'Impossible de retirer le farmer, champ obligatoire.'], 400);
            }
        }

        $em->flush();

        return $this->json($product, 200, [], ['groups' => 'product:read']);
    }

    /**
     * DELETE /api/products/{id}
     * Supprime un produit
     */
    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(int $id, ProductRepository $productRepository, EntityManagerInterface $em): JsonResponse
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw new NotFoundHttpException('Produit introuvable');
        }

        $em->remove($product);
        $em->flush();

        return $this->json(null, 204);
    }
}

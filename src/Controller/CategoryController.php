<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
#[Route('/api/categories')]
final class CategoryController extends AbstractController
{
    /**
     * Liste toutes les catégories
     * Méthode : GET /api/categories
     */
    #[Route('', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): JsonResponse
    {
        $categories = $categoryRepository->findAll();

        // On renvoie les données en JSON.
        // Le 3ème paramètre [] représente les en-têtes HTTP supplémentaires.
        // Le 4ème paramètre permet de préciser le groupe de sérialisation.
        return $this->json($categories, 200, [], ['groups' => 'category:read']);
    }

    /**
     * Récupère une catégorie par son ID
     * Méthode : GET /api/categories/{id}
     */
    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(int $id, CategoryRepository $categoryRepository): JsonResponse
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException('Catégorie introuvable');
        }

        return $this->json($category, 200, [], ['groups' => 'category:read']);
    }

    /**
     * Crée une nouvelle catégorie
     * Méthode : POST /api/categories
     * Body attendu en JSON : { "name": "Fruits" }
     */
    #[Route('', name: 'app_category_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse {
        // Récupération des données en JSON
        $data = json_decode($request->getContent(), true);

        // Validation minimale
        if (!isset($data['name']) || empty($data['name'])) {
            return $this->json(['error' => 'Le nom de la catégorie est obligatoire.'], 400);
        }

        // Création de la nouvelle catégorie
        $category = new Category();
        $category->setName($data['name']);

        // Persistance en base de données
        $em->persist($category);
        $em->flush();

        // On renvoie la catégorie créée avec un code 201 (Created)
        return $this->json($category, 201, [], ['groups' => 'category:read']);
    }

    /**
     * Modifie une catégorie existante
     * Méthode : PUT /api/categories/{id}
     * Body attendu en JSON : { "name": "Légumes" }
     */
    #[Route('/{id}', name: 'app_category_update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        // Récupérer la catégorie
        $category = $categoryRepository->find($id);
        if (!$category) {
            throw new NotFoundHttpException('Catégorie introuvable');
        }

        // Récupérer les données JSON
        $data = json_decode($request->getContent(), true);

        // Mettre à jour le nom si présent dans la requête
        if (isset($data['name']) && !empty($data['name'])) {
            $category->setName($data['name']);
        }

        // Persister la modification
        $em->flush();

        return $this->json($category, 200, [], ['groups' => 'category:read']);
    }

    /**
     * Supprime une catégorie
     * Méthode : DELETE /api/categories/{id}
     */
    #[Route('/{id}', name: 'app_category_delete', methods: ['DELETE'])]
    public function delete(int $id, CategoryRepository $categoryRepository, EntityManagerInterface $em): JsonResponse {
        // Récupérer la catégorie
        $category = $categoryRepository->find($id);
        if (!$category) {
            throw new NotFoundHttpException('Catégorie introuvable');
        }

        // Supprimer en base
        $em->remove($category);
        $em->flush();

        // Renvoie un No Content 204
        return $this->json(null, 204);
    }
}

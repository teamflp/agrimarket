<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CategoryService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CategoryRepository     $categoryRepository,
        private ValidatorInterface     $validator
    ) {
    }

    public function createCategory(Category $category): Category
    {
        $this->validateCategory($category);
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    public function updateCategory(int $id, Category $updatedCategory): ?Category
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            return null;
        }

        $category->setName($updatedCategory->getName());
        $this->validateCategory($category); // Valider après la mise à jour également

        $this->entityManager->flush();

        return $category;
    }

    public function deleteCategory(int $id): bool
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            return false;
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return true;
    }

    private function validateCategory(Category $category): void
    {
        $constraints = new Assert\Collection([
            'name' => [
                new Assert\NotBlank(message: 'Le nom de la catégorie ne peut pas être vide.'),
                new Assert\Length(max: 255, maxMessage: 'Le nom de la catégorie ne peut pas dépasser {{ limit }} caractères.'),
            ],
            'description' => [
                new Assert\Length(max: 500, maxMessage: 'La description de la catégorie ne peut pas dépasser {{ limit }} caractères.'),
            ],
        ]);

        $violations = $this->validator->validate($category, $constraints);

        if (count($violations) > 0) {
            $errorMessages = [];
            foreach ($violations as $violation) {
                $errorMessages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            throw new BadRequestHttpException(implode(', ', $errorMessages));
        }
    }
}
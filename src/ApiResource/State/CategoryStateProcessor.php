<?php

namespace App\ApiResource\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Category;
use App\Service\CategoryService;

readonly class CategoryStateProcessor implements ProcessorInterface
{
    public function __construct(private CategoryService $categoryService)
    {
    }

    /**
     * @param Category $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Post) {
            return $this->categoryService->createCategory($data);
        }

        if ($operation instanceof Put) {
            return $this->categoryService->updateCategory($uriVariables['id'], $data);
        }

        if ($operation instanceof Delete) {
            return $this->categoryService->deleteCategory($uriVariables['id']);
        }

        return $data; // For Get and GetCollection operations, we don't need to process.
    }
}
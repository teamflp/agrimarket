<?php

namespace App\Service;

use App\Entity\OrderItem;
use App\Repository\OrderItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class OrderItemService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private OrderItemRepository $orderItemRepository
    ) {
    }

    public function createOrderItem(OrderItem $orderItem): OrderItem
    {
        $this->validateOrderItem($orderItem);

        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();

        return $orderItem;
    }

    public function updateOrderItem(int $id, OrderItem $updatedOrderItem): ?OrderItem
    {
        $orderItem = $this->orderItemRepository->find($id);

        if (!$orderItem) {
            return null;
        }

        $orderItem->setProduct($updatedOrderItem->getProduct());
        $orderItem->setQuantity($updatedOrderItem->getQuantity());
        $orderItem->setUnitPrice($updatedOrderItem->getUnitPrice());

        $this->validateOrderItem($orderItem);

        $this->entityManager->flush();

        return $orderItem;
    }

    public function deleteOrderItem(int $id): bool
    {
        $orderItem = $this->orderItemRepository->find($id);

        if (!$orderItem) {
            return false;
        }

        $this->entityManager->remove($orderItem);
        $this->entityManager->flush();

        return true;
    }

    public function getOrderItemById(int $id): ?OrderItem
    {
        return $this->orderItemRepository->find($id);
    }

    private function validateOrderItem(OrderItem $orderItem): void
    {
        if (!$orderItem->getProduct()) {
            throw new BadRequestHttpException('Chaque article doit avoir un produit assigné.');
        }

        $violations = $this->validator->validate($orderItem);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            throw new BadRequestHttpException(implode(', ', $errors));
        }
    }
}

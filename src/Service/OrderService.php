<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use DateTimeImmutable;

readonly class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private OrderRepository $orderRepository,
        private ValidatorInterface $validator
    ) {
    }

    public function createOrder(Order $order, User $buyer): Order
    {
        $order->setCreatedAt(new DateTimeImmutable());
        $order->setBuyer($buyer);

        // Valider et préparer les OrderItems (prix unitaire, stock, etc.)
        $this->processOrderItems($order);

        // Calculer le total après avoir préparé les items
        $order->setTotal($this->calculateTotal($order));

        // Valider les contraintes Symfony
        $this->validateOrder($order);

        // Décrémenter les stocks des produits associés aux OrderItems
        foreach ($order->getOrderItems() as $item) {
            $product = $item->getProduct();

            $newQty = $product->getQuantity() - $item->getQuantity();
            if ($newQty < 0) {
                throw new BadRequestHttpException("Erreur interne : stock négatif pour le produit '{$product->getName()}'.");
            }

            $product->setQuantity($newQty);
        }

        // Persister la commande et les changements produits
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    public function updateOrder(int $id, Order $updatedOrder, ?User $buyer = null): ?Order
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            return null;
        }

        $order->setBuyer($buyer ?? $updatedOrder->getBuyer());
        $order->setStatus($updatedOrder->getStatus());

        $this->processOrderItems($updatedOrder);
        $order->setOrderItems($updatedOrder->getOrderItems());

        $order->setTotal($this->calculateTotal($order));

        $this->validateOrder($order);

        $this->entityManager->flush();

        return $order;
    }

    public function deleteOrder(int $id): bool
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            return false;
        }

        $this->entityManager->remove($order);
        $this->entityManager->flush();

        return true;
    }

    public function getOrderById(int $id): ?Order
    {
        return $this->orderRepository->find($id);
    }

    /**
     * Calcule le total TTC d'une commande.
     */
    private function calculateTotal(Order $order): string
    {
        $total = 0;

        foreach ($order->getOrderItems() as $item) {
            if ($item->getUnitPrice() !== null && $item->getQuantity() !== null) {
                $total += (float) $item->getUnitPrice() * $item->getQuantity();
            }
        }

        return number_format($total, 2, '.', '');
    }

    /**
     * Prépare les OrderItems :
     * - Vérifie les produits
     * - Vérifie la quantité > 0
     * - Vérifie le stock disponible
     * - Détermine automatiquement le prix unitaire
     */
    private function processOrderItems(Order $order): void
    {
        foreach ($order->getOrderItems() as $item) {
            $product = $item->getProduct();

            if (!$product) {
                throw new BadRequestHttpException('Chaque article doit être lié à un produit.');
            }

            if ($item->getQuantity() <= 0) {
                throw new BadRequestHttpException("La quantité du produit '{$product->getName()}' doit être supérieure à 0.");
            }

            if ($item->getQuantity() > $product->getQuantity()) {
                throw new BadRequestHttpException("Stock insuffisant pour le produit : '{$product->getName()}'.");
            }

            // Définir automatiquement le prix unitaire depuis le produit
            $item->setUnitPrice($product->getPrice());
        }
    }

    /**
     * Valide l'entité commande avec le composant Validator de Symfony.
     */
    private function validateOrder(Order $order): void
    {
        if (count($order->getOrderItems()) < 1) {
            throw new BadRequestHttpException('La commande doit contenir au moins un article.');
        }

        $violations = $this->validator->validate($order);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            throw new BadRequestHttpException(implode(', ', $errors));
        }
    }
}

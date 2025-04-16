<?php

namespace App\ApiResource\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\OrderItem;
use App\Service\OrderItemService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class OrderItemStateProcessor implements ProcessorInterface
{
    public function __construct(
        private OrderItemService $orderItemService
    ) {
    }

    /**
     * @param OrderItem $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Post) {
            return $this->orderItemService->createOrderItem($data);
        }

        if ($operation instanceof Put) {
            $id = $uriVariables['id'] ?? null;

            if (!$id) {
                throw new NotFoundHttpException('Identifiant de l’article manquant pour la mise à jour.');
            }

            return $this->orderItemService->updateOrderItem((int) $id, $data);
        }

        if ($operation instanceof Delete) {
            $id = $uriVariables['id'] ?? null;

            if (!$id) {
                throw new NotFoundHttpException('Identifiant de l’article manquant pour la suppression.');
            }

            $this->orderItemService->deleteOrderItem((int) $id);
            return null;
        }

        return $data;
    }
}

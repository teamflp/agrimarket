<?php

namespace App\ApiResource\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Order;
use App\Entity\User;
use App\Service\OrderService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use RuntimeException;

readonly class OrderStateProcessor implements ProcessorInterface
{
    public function __construct(
        private OrderService $orderService,
        private ?TokenStorageInterface $tokenStorage = null
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $currentUser = $this->getCurrentUser();

        if (!$currentUser instanceof User) {
            throw new RuntimeException('Utilisateur non authentifié.');
        }

        return match (true) {
            $operation instanceof Post => $this->orderService->createOrder($data, $currentUser),

            $operation instanceof Put => $this->handleUpdate($data, $uriVariables, $currentUser),

            $operation instanceof Delete => $this->orderService->deleteOrder((int) $uriVariables['id']),

            default => $data,
        };
    }

    private function handleUpdate(Order $data, array $uriVariables, User $currentUser): Order
    {
        $existingOrder = $this->orderService->getOrderById((int) $uriVariables['id']);

        if (!$existingOrder) {
            throw new NotFoundHttpException('Commande non trouvée.');
        }

        $data->setCreatedAt($existingOrder->getCreatedAt()); // On conserve la date d’origine
        return $this->orderService->updateOrder((int) $uriVariables['id'], $data, $currentUser);
    }

    private function getCurrentUser(): ?UserInterface
    {
        $token = $this->tokenStorage?->getToken();

        return $token?->getUser() instanceof UserInterface
            ? $token->getUser()
            : null;
    }
}

<?php

namespace App\ApiResource\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Notification;
use App\Entity\User;
use App\Service\NotificationService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class NotificationStateProcessor implements ProcessorInterface
{
    public function __construct(
        private NotificationService $notificationService,
        private readonly ?TokenStorageInterface $tokenStorage = null
    ) {
    }

    /**
     * @param Notification $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $currentUser = $this->getCurrentUser();

        if (!$currentUser instanceof User) {
            throw new \RuntimeException('Utilisateur non trouvé.');
        }

        if ($operation instanceof Post) {
            return $this->notificationService->createNotification($data, $currentUser);
        }

        if ($operation instanceof Put) {
            return $this->notificationService->updateNotification($uriVariables['id'], $data, $currentUser);
        }

        if ($operation instanceof Delete) {
            return $this->notificationService->deleteNotification($uriVariables['id']);
        }

        return $data;
    }

    private function getCurrentUser(): ?UserInterface
    {
        $token = $this->tokenStorage?->getToken();

        if (null === $token) {
            return null;
        }

        $user = $token->getUser();

        return $user instanceof UserInterface ? $user : null;
    }
}
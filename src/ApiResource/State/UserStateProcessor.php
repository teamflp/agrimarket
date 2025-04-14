<?php

namespace App\ApiResource\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Service\UserService;

readonly class UserStateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserService $userService,
        private ProcessorInterface $persistProcessor,
        private ProcessorInterface $removeProcessor,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof User) {
            return $this->delegateToStandardProcessor($data, $operation, $uriVariables, $context);
        }

        $this->applyBusinessLogic($data, $operation);
        return $this->delegatePersistence($data, $operation, $uriVariables, $context);
    }

    private function delegateToStandardProcessor(
        mixed $data,
        Operation $operation,
        array $uriVariables,
        array $context
    ): mixed {
        return $operation->canWrite()
            ? $this->persistProcessor->process($data, $operation, $uriVariables, $context)
            : $data;
    }

    private function applyBusinessLogic(User $user, Operation $operation): void
    {
        if ($operation->getName() === 'post') {
            $this->userService->createUser($user);
        } else {
            $this->userService->updateUser($user);
        }
    }

    private function delegatePersistence(
        User $user,
        Operation $operation,
        array $uriVariables,
        array $context
    ): User {
        return $operation instanceof Delete
            ? $this->handleDeletion($user, $operation, $uriVariables, $context)
            : $this->persistProcessor->process($user, $operation, $uriVariables, $context);
    }

    private function handleDeletion(
        User $user,
        Operation $operation,
        array $uriVariables,
        array $context
    ): User {
        $this->removeProcessor->process($user, $operation, $uriVariables, $context);
        return $user;
    }
}
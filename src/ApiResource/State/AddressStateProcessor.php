<?php

namespace App\ApiResource\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Service\AddressService;

readonly class AddressStateProcessor implements ProcessorInterface
{
    public function __construct(private AddressService $addressService)
    {
    }

    /**
     * @param mixed $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Post) {
            return $this->addressService->createAddress($data);
        }

        if ($operation instanceof Put) {
            return $this->addressService->updateAddress($uriVariables['id'], $data);
        }

        if ($operation instanceof Delete) {
            $this->addressService->deleteAddress($uriVariables['id']);
            return null;
        }

        return $data; // Pour les opérations Get et GetCollection, on ne fait rien de plus.
    }

}
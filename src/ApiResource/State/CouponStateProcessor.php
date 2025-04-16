<?php

namespace App\ApiResource\State;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Coupon;
use App\Service\CouponService;

readonly class CouponStateProcessor implements ProcessorInterface
{
    public function __construct(private CouponService $couponService)
    {
    }

    /**
     * @param Coupon $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Post) {
            return $this->couponService->createCoupon($data);
        }

        if ($operation instanceof Put) {
            return $this->couponService->updateCoupon($uriVariables['id'], $data);
        }

        if ($operation instanceof Delete) {
            return $this->couponService->deleteCoupon($uriVariables['id']);
        }

        return $data;
    }
}
<?php

namespace App\Service;

use App\Entity\Coupon;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\CouponRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CouponService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CouponRepository     $couponRepository,
        private ValidatorInterface     $validator
    ) {
    }

    public function createCoupon(Coupon $coupon): Coupon
    {
        $this->validateCoupon($coupon);
        $this->entityManager->persist($coupon);
        $this->entityManager->flush();

        return $coupon;
    }

    public function updateCoupon(int $id, Coupon $updatedCoupon): ?Coupon
    {
        $coupon = $this->couponRepository->find($id);

        if (!$coupon) {
            return null;
        }

        $coupon->setCode($updatedCoupon->getCode());
        $coupon->setDiscountType($updatedCoupon->getDiscountType());
        $coupon->setValue($updatedCoupon->getValue());
        $coupon->setExpirationDate($updatedCoupon->getExpirationDate());
        $coupon->setUsageLimit($updatedCoupon->getUsageLimit());
        $coupon->setUser($updatedCoupon->getUser()); // Mise à jour de la relation User
        $coupon->setOrder($updatedCoupon->getOrder()); // Mise à jour de la relation Order

        $this->validateCoupon($coupon); // Valider après la mise à jour également

        $this->entityManager->flush();

        return $coupon;
    }

    public function deleteCoupon(int $id): bool
    {
        $coupon = $this->couponRepository->find($id);

        if (!$coupon) {
            return false;
        }

        $this->entityManager->remove($coupon);
        $this->entityManager->flush();

        return true;
    }

    private function validateCoupon(Coupon $coupon): void
    {
        $constraints = new Assert\Collection([
            'code' => [
                new Assert\NotBlank(message: 'Le code du coupon ne peut pas être vide.'),
                new Assert\Length(max: 255, maxMessage: 'Le code du coupon ne peut pas dépasser {{ limit }} caractères.'),
            ],
            'discountType' => [
                new Assert\NotBlank(message: 'Le type de réduction doit être spécifié.'),
                new Assert\Choice(choices: ['percentage', 'fixed'], message: 'Le type de réduction doit être "percentage" ou "fixed".'),
            ],
            'value' => [
                new Assert\NotBlank(message: 'La valeur de la réduction ne peut pas être vide.'),
                new Assert\Type(type: 'float', message: 'La valeur de la réduction doit être un nombre.'),
                new Assert\GreaterThan(value: 0, message: 'La valeur de la réduction doit être supérieure à 0.'),
            ],
            'expirationDate' => [
                new Assert\NotNull(message: 'La date d\'expiration ne peut pas être nulle.'),
                new Assert\Type(type: \DateTimeInterface::class, message: 'La date d\'expiration doit être une date valide.'),
                new Assert\GreaterThan(value: new \DateTime('now'), message: 'La date d\'expiration doit être dans le futur.'),
            ],
            'usageLimit' => [
                new Assert\PositiveOrZero(message: 'La limite d\'utilisation doit être un nombre positif ou zéro.'),
                new Assert\Type(type: 'integer', message: 'La limite d\'utilisation doit être un entier.'),
            ],
            'user' => [
                new Assert\Type(type: User::class, message: 'L\'utilisateur associé doit être une instance de User.'),
            ],
            'order' => [
                new Assert\Type(type: Order::class, message: 'La commande associée doit être une instance de Order.'),
            ],
        ]);

        $violations = $this->validator->validate($coupon, $constraints);

        if (count($violations) > 0) {
            $errorMessages = [];
            foreach ($violations as $violation) {
                $errorMessages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            throw new BadRequestHttpException(implode(', ', $errorMessages));
        }
    }
}
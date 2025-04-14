<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class UserService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator,
    ) {}

    public function createUser(User $user): User
    {
        $this->validateUser($user);
        $this->hashPassword($user);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    public function updateUser(User $user): User
    {
        $this->validateUser($user);
        $this->hashPassword($user);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    private function validateUser(User $user): void
    {
        $constraints = new Assert\Collection([
            'firstName' => [new Assert\NotBlank(), new Assert\Length(['min' => 2])],
            'lastName' => [new Assert\NotBlank(), new Assert\Length(['min' => 2])],
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'password' => [
                new Assert\NotBlank(),
                new Assert\Regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z0-9]).{8,}$/'),
            ],
        ]);

        $violations = $this->validator->validate($user, $constraints);

        if (count($violations) > 0) {
            $errorMessages = [];
            foreach ($violations as $violation) {
                $errorMessages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            throw new BadRequestHttpException(implode(', ', $errorMessages));
        }
    }

    private function hashPassword(User $user): void
    {
        if ($user->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
        }
    }
}
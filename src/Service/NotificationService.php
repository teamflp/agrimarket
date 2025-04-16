<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

readonly class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotificationRepository $notificationRepository,
        private ValidatorInterface     $validator
    ) {
    }

    public function createNotification(Notification $notification, User $user): Notification
    {
        $notification->setCreateAt(new DateTimeImmutable());
        $notification->setUser($user);
        $this->validateNotification($notification);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }

    public function updateNotification(int $id, Notification $updatedNotification, User $user): ?Notification
    {
        $notification = $this->notificationRepository->find($id);

        if (!$notification) {
            return null;
        }

        $notification->setTitle($updatedNotification->getTitle());
        $notification->setContent($updatedNotification->getContent());
        $notification->setChannel($updatedNotification->getChannel());
        $notification->setUser($user); // Ensure user is updated
        $this->validateNotification($notification);

        $this->entityManager->flush();

        return $notification;
    }

    public function deleteNotification(int $id): bool
    {
        $notification = $this->notificationRepository->find($id);

        if (!$notification) {
            return false;
        }

        $this->entityManager->remove($notification);
        $this->entityManager->flush();

        return true;
    }

    private function validateNotification(Notification $notification): void
    {
        $constraints = new Assert\Collection([
            'title' => [
                new Assert\NotBlank(message: 'Le titre de la notification est requis.'),
                new Assert\Length(max: 255, maxMessage: 'Le titre de la notification ne peut pas dépasser {{ limit }} caractères.'),
            ],
            'content' => [
                new Assert\NotBlank(message: 'Le contenu de la notification est requis.'),
            ],
            'channel' => [
                new Assert\NotBlank(message: 'Le canal de notification est requis.'),
                new Assert\Choice(choices: ['email', 'sms', 'push'], message: 'Le canal de notification doit être "email", "sms" ou "push".'),
            ],
            'createAt' => [
                new Assert\DateTime(message: 'La date de création doit être une date valide.'),
            ],
            'user' => [
                new Assert\NotNull(message: 'L\'utilisateur associé à la notification est requis.'),
                new Assert\Type(type: User::class, message: 'L\'utilisateur associé doit être une instance de User.'),
            ],
        ]);

        $violations = $this->validator->validate($notification, $constraints);

        if (count($violations) > 0) {
            $errorMessages = [];
            foreach ($violations as $violation) {
                $errorMessages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            throw new BadRequestHttpException(implode(', ', $errorMessages));
        }
    }
}
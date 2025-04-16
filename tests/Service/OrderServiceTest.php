<?php

namespace App\Tests\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\User;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OrderServiceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private OrderService $orderService;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->orderService = $container->get(OrderService::class);

        // Nettoyage optionnel avant chaque test (si base en mémoire ou fixtures)
        $this->entityManager->createQuery('DELETE FROM App\Entity\Order')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Product')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
    }

    public function testCreateOrderSuccessfully(): void
    {
        // 1. Créer un utilisateur
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password');
        $this->entityManager->persist($user);

        // 2. Créer un produit
        $product = new Product();
        $product->setName('Bible');
        $product->setPrice(10.00);
        $product->setQuantity(100);
        $this->entityManager->persist($product);

        $this->entityManager->flush();

        // 3. Créer un OrderItem
        $item = new OrderItem();
        $item->setProduct($product);
        $item->setQuantity(2);

        // 4. Créer la commande
        $order = new Order();
        $order->addOrderItem($item);

        // 5. Appel au service
        $createdOrder = $this->orderService->createOrder($order, $user);

        // 6. Assert
        $this->assertNotNull($createdOrder->getId());
        $this->assertEquals('20.00', $createdOrder->getTotal());
        $this->assertEquals($user->getId(), $createdOrder->getBuyer()->getId());
        $this->assertCount(1, $createdOrder->getOrderItems());

        // Vérifie que le stock a été décrémenté
        $this->entityManager->refresh($product);
        $this->assertEquals(98, $product->getQuantity());
    }

    public function testCreateOrderWithInsufficientStock(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage("Stock insuffisant");

        $user = new User();
        $user->setEmail('stock@fail.com');
        $user->setPassword('fail');
        $this->entityManager->persist($user);

        $product = new Product();
        $product->setName('Chants d’espérance');
        $product->setPrice(25.00);
        $product->setQuantity(1); // Stock faible
        $this->entityManager->persist($product);

        $this->entityManager->flush();

        $item = new OrderItem();
        $item->setProduct($product);
        $item->setQuantity(3); // Trop demandé

        $order = new Order();
        $order->addOrderItem($item);

        $this->orderService->createOrder($order, $user);
    }
}
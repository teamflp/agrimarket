<?php

namespace App\Tests\Service;

use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\OrderItemRepository;
use App\Service\OrderItemService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderItemServiceTest extends TestCase
{
    private OrderItemService $service;
    private EntityManagerInterface $entityManager;
    private OrderItemRepository $orderItemRepository;
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->orderItemRepository = $this->createMock(OrderItemRepository::class);
        $this->validator = $this->createMock(ValidatorInterface::class);

        $this->service = new OrderItemService(
            $this->entityManager,
            $this->orderItemRepository,
            $this->validator
        );
    }

    public function testCreateOrderItemSuccess(): void
    {
        $product = (new Product())->setPrice("10.00")->setQuantity(50);
        $orderItem = (new OrderItem())->setProduct($product)->setQuantity(5);

        // Simuler la validation sans violation
        $this->validator
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        // Simuler la persistance de l'OrderItem
        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($orderItem);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        // Appeler la méthode
        $created = $this->service->createOrderItem($orderItem);

        // Assertions
        $this->assertSame($orderItem, $created);
        $this->assertEquals("10.00", $created->getUnitPrice());
    }

    public function testCreateOrderItemInvalidProduct(): void
    {
        $orderItem = new OrderItem();

        // Vérifier que l'exception est levée si l'OrderItem n'a pas de produit
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Chaque OrderItem doit être lié à un produit.');

        $this->service->createOrderItem($orderItem);
    }

    public function testCreateOrderItemNegativeQuantity(): void
    {
        $product = (new Product())->setName("Produit A")->setPrice("10.00")->setQuantity(10);
        $orderItem = (new OrderItem())->setProduct($product)->setQuantity(-5);

        // Vérifier que l'exception est levée si la quantité est négative
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage("La quantité du produit 'Produit A' doit être supérieure à 0.");

        $this->service->createOrderItem($orderItem);
    }

    public function testCreateOrderItemInsufficientStock(): void
    {
        $product = (new Product())->setName("Produit B")->setPrice("15.00")->setQuantity(3);
        $orderItem = (new OrderItem())->setProduct($product)->setQuantity(5);

        // Vérifier que l'exception est levée si le stock est insuffisant
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage("Stock insuffisant pour le produit : 'Produit B'.");

        $this->service->createOrderItem($orderItem);
    }

    public function testCreateOrderItemValidationFails(): void
    {
        $product = (new Product())->setName("Produit X")->setPrice("20.00")->setQuantity(50);
        $orderItem = (new OrderItem())->setProduct($product)->setQuantity(3);

        // Création d'une violation de contrainte pour la quantité
        $violations = new ConstraintViolationList([
            new ConstraintViolation("La quantité doit être supérieure à 0", "message", [], "", "quantity", 3)
        ]);

        // Simuler une validation échouée avec une violation
        $this->validator
            ->method('validate')
            ->willReturn($violations);

        // Vérifier que l'exception est levée
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('quantity: La quantité doit être supérieure à 0');

        $this->service->createOrderItem($orderItem);
    }
}

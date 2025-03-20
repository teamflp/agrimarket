<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\OrderItem;
use App\Entity\Rating;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testSetNameAndGetName(): void
    {
        $product = new Product();
        $product->setName('Pommes');

        $this->assertEquals('Pommes', $product->getName());
    }

    public function testSetGetFarmer(): void
    {
        $product = new Product();
        $user = new User(); // c'est juste un new, on ne teste pas User ici
        $product->setFarmer($user);

        $this->assertSame($user, $product->getFarmer());
    }

    public function testAddRemoveOrderItem(): void
    {
        $product = new Product();
        $orderItem = new OrderItem();

        // Par défaut, la collection est vide
        $this->assertCount(0, $product->getOrderItems());

        // Ajouter un OrderItem
        $product->addOrderItem($orderItem);
        $this->assertCount(1, $product->getOrderItems());
        $this->assertSame($product, $orderItem->getProduct());

        // Retirer un OrderItem
        $product->removeOrderItem($orderItem);
        $this->assertCount(0, $product->getOrderItems());
        $this->assertNull($orderItem->getProduct());
    }

    public function testAddRemoveRating(): void
    {
        $product = new Product();
        $rating = new Rating();

        $this->assertCount(0, $product->getRatings());

        $product->addRating($rating);
        $this->assertCount(1, $product->getRatings());
        $this->assertSame($product, $rating->getProduct());

        $product->removeRating($rating);
        $this->assertCount(0, $product->getRatings());
        $this->assertNull($rating->getProduct());
    }
}

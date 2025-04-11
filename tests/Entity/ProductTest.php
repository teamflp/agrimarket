<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Category;
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

    public function testSetSlugAndGetSlug(): void
    {
        $product = new Product();
        $product->setSlug('pommes');
        $this->assertEquals('pommes', $product->getSlug());
    }

    public function testSetDescriptionAndGetDescription(): void
    {
        $product = new Product();
        $product->setDescription('Des pommes délicieuses');
        $this->assertEquals('Des pommes délicieuses', $product->getDescription());
    }

    public function testSetPriceAndGetPrice(): void
    {
        $product = new Product();
        $product->setPrice(2.50);
        $this->assertEquals(2.50, $product->getPrice());
    }

    public function testSetQuantityAndGetQuantity(): void
    {
        $product = new Product();
        $product->setQuantity(10);
        $this->assertEquals(10, $product->getQuantity());
    }

    public function testSetCategoryAndGetCategory(): void
    {
        $product = new Product();
        $category = new Category();
        $product->setCategory($category);
        $this->assertSame($category, $product->getCategory());
    }

    public function testSetFarmerAndGetFarmer(): void
    {
        $product = new Product();
        $user = new User();
        $product->setFarmer($user);
        $this->assertSame($user, $product->getFarmer());
    }

    public function testAddRemoveOrderItem(): void
    {
        $product = new Product();
        $orderItem = new OrderItem();
        $this->assertCount(0, $product->getOrderItems());
        $product->addOrderItem($orderItem);
        $this->assertCount(1, $product->getOrderItems());
        $this->assertSame($product, $orderItem->getProduct());
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

    public function testSetIllustrationAndGetIllustration(): void
    {
        $product = new Product();
        $product->setIllustration('pommes.jpg');
        $this->assertEquals('pommes.jpg', $product->getIllustration());
    }
}
<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    public function indexDisplaysProductsSuccessfully(): void
    {
        $client = static::createClient();
        $client->request('GET', '/product');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function createValidProduct(): void
    {
        $client = static::createClient();
        $client->request('POST', '/product', [
            'json' => [
                'name' => 'New Example',
                'slug' => 'new-example',
                'description' => 'A valid product',
                'price' => '50',
                'quantity' => 10,
                'illustration' => 'image.jpg'
            ],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function createProductWithMissingFields(): void
    {
        $client = static::createClient();
        $client->request('POST', '/product', [
            'json' => [
                'name' => '',
                'slug' => 'no-name',
                'description' => 'Missing a name',
                'price' => '30',
                'quantity' => 5,
                'illustration' => 'img.jpg'
            ],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function showNonExistentProductFails(): void
    {
        $client = static::createClient();
        $client->request('GET', '/product/9999');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function updateProductSuccessfully(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/product/1', [
            'json' => [
                'name' => 'Updated Name',
                'slug' => 'updated-name',
                'description' => 'Updated description',
                'price' => '75',
                'quantity' => 20,
                'illustration' => 'updated.jpg'
            ],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function deleteProductAsUnauthorizedUserFails(): void
    {
        $client = static::createClient([], ['HTTP_X-AUTH-TOKEN' => 'invalid-token']);
        $client->request('DELETE', '/product/1');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
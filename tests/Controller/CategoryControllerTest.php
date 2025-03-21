<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CategoryControllerTest extends WebTestCase
{
    /**
     * Teste la route GET /api/categories (index)
     * On s'attend à un code 200 et du JSON
     */
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/categories');

        // Vérifier que la requête est un succès (2xx)
        self::assertResponseIsSuccessful();

        // Vérifier que la réponse est au format JSON
        self::assertResponseFormatSame('json');

        // Vérifier le contenu retourné
        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertIsArray($data); // On s'attend à un tableau (liste de catégories)
    }

    /**
     * Teste la création d'une nouvelle catégorie (POST /api/categories)
     */
    public function testCreateCategory(): void
    {
        $client = static::createClient();

        $payload = ['name' => 'Fruits'];
        $client->request(
            'POST',
            '/api/categories',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        self::assertResponseStatusCodeSame(201);

        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('id', $data);
        self::assertArrayHasKey('name', $data);
        self::assertEquals('Fruits', $data['name']);
    }

    /**
     * Teste la création avec une donnée invalide (nom manquant) -> 400
     */
    public function testCreateCategoryInvalid(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/categories',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([]) // pas de "name"
        );

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * Teste la récupération d'une catégorie : GET /api/categories/{id}
     */
    public function testShowCategory(): void
    {
        $client = static::createClient();

        // 1) On crée d'abord une catégorie
        $payload = ['name' => 'Légumes'];
        $client->request(
            'POST',
            '/api/categories',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $createdId = $data['id'] ?? null;
        self::assertNotNull($createdId, 'La catégorie doit avoir un ID après la création.');

        // 2) On effectue la requête GET /api/categories/{id}
        $client->request('GET', '/api/categories/' . $createdId);

        self::assertResponseIsSuccessful();
        self::assertResponseFormatSame('json');

        $fetchedData = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('Légumes', $fetchedData['name']);
    }

    /**
     * Teste la modification (PUT /api/categories/{id})
     */
    public function testUpdateCategory(): void
    {
        $client = static::createClient();

        // 1) Créer une catégorie
        $payload = ['name' => 'Pommes'];
        $client->request(
            'POST',
            '/api/categories',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $createdId = $data['id'] ?? null;
        self::assertNotNull($createdId, 'La catégorie doit avoir un ID après la création.');

        // 2) Mettre à jour la catégorie (PUT)
        $updatePayload = ['name' => 'Pommes BIO'];
        $client->request(
            'PUT',
            '/api/categories/' . $createdId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updatePayload)
        );

        self::assertResponseIsSuccessful();
        $updatedData = json_decode($client->getResponse()->getContent(), true);
        self::assertEquals('Pommes BIO', $updatedData['name']);
    }

    /**
     * Teste la suppression (DELETE /api/categories/{id})
     */
    public function testDeleteCategory(): void
    {
        $client = static::createClient();

        // 1) Créer une catégorie à supprimer
        $payload = ['name' => 'À Supprimer'];
        $client->request(
            'POST',
            '/api/categories',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('id', $data);
        $idToDelete = $data['id'];
        self::assertNotNull($idToDelete);

        // 2) Supprimer la catégorie
        $client->request('DELETE', '/api/categories/' . $idToDelete);
        self::assertResponseStatusCodeSame(204);

        // 3) Vérifier qu'elle n'existe plus
        $client->request('GET', '/api/categories/' . $idToDelete);
        self::assertResponseStatusCodeSame(404);
    }
}
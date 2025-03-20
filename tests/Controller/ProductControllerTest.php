<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    private $client;        // Le client HTTP
    private $em;            // L'EntityManager pour les opérations en BDD

    /**
     * Avant chaque test, on réinitialise la base de test :
     * - Suppression de toutes les lignes
     * - Reset des auto-increments (si MySQL)
     * - Création d'un User (ID=1) et d'une Category (ID=2)
     */
    protected function setUp(): void
    {
        // Ne pas appeler parent::setUp(), qui booterait déjà le kernel
        // parent::setUp();

        // 1) Créer un seul client (boot du kernel)
        $this->client = static::createClient();

        // 2) Récupérer l'EntityManager pour manipuler la base
        $this->em = $this->client->getContainer()->get('doctrine')->getManager();

        // 3) Nettoyer les tables pour éviter les conflits entre tests
        $this->em->createQuery('DELETE FROM App\Entity\Product')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Category')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\User')->execute();

        // 4) Ré-initialiser les auto-increments (optionnel, MySQL seulement)
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();
        if ('mysql' === $platform->getName()) {
            $connection->executeStatement('ALTER TABLE product AUTO_INCREMENT = 1');
            $connection->executeStatement('ALTER TABLE category AUTO_INCREMENT = 1');
            $connection->executeStatement('ALTER TABLE user AUTO_INCREMENT = 1');
        }

        // 5) Créer un User (sera ID=1)
        $user = new User();
        $user->setEmail('farmer@test.com');
        $user->setPassword('secret123');
        $user->setPhoneNumber('0123456789'); // <-- important, puisque not null
        $user->setRoles(['ROLE_FARMER']);

        $this->em->persist($user);

        // 6) Créer une Category (sera ID=2)
        $category = new Category();
        $category->setName('Test Category');
        $this->em->persist($category);

        // 7) Valider en base
        $this->em->flush();
    }

    /**
     * Teste la liste des produits : GET /api/products
     */
    public function testIndex(): void
    {
        // Réutilise le client créé en setUp()
        $this->client->request('GET', '/api/products');

        self::assertResponseIsSuccessful();
        self::assertResponseFormatSame('json');
    }

    /**
     * Teste la création d'un produit : POST /api/products
     */
    public function testCreateProduct(): void
    {
        // Désormais farmer=1 et category=2 sont sûrs d'exister
        $payload = [
            'name' => 'Orange',
            'slug' => 'orange',
            'description' => 'C\'est un fruit sucré',
            'price' => '3',
            'quantity' => 50,
            'category' => 2,
            'farmer' => 1,
            'illustration' => 'orange.jpg'
        ];

        $this->client->request(
            'POST',
            '/api/products',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload)
        );

        self::assertResponseStatusCodeSame(201);
        self::assertResponseFormatSame('json');

        $data = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('id', $data);
        self::assertEquals('Orange', $data['name']);
    }

    /**
     * Teste la récupération d'un produit : GET /api/products/{id}
     */
    public function testShowProduct(): void
    {
        // 1) Créer un produit
        $payload = [
            'name' => 'Pomme',
            'slug' => 'pomme',
            'description' => 'Une pomme test',
            'price' => '2',
            'quantity' => 10,
            'category' => 2,
            'farmer' => 1,
            'illustration' => 'pomme.jpg'
        ];

        $this->client->request(
            'POST',
            '/api/products',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload)
        );

        self::assertResponseStatusCodeSame(201);
        self::assertResponseFormatSame('json');

        $productData = json_decode($this->client->getResponse()->getContent(), true);
        $id = $productData['id'] ?? null;
        self::assertNotNull($id, 'Le product ID ne doit pas être null après la création.');

        // 2) GET /api/products/{id}
        $this->client->request('GET', '/api/products/' . $id);

        self::assertResponseIsSuccessful();
        self::assertResponseFormatSame('json');

        $fetchedData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals('Pomme', $fetchedData['name']);
    }

    /**
     * Teste la modification d'un produit : PUT /api/products/{id}
     */
    public function testUpdateProduct(): void
    {
        // 1) Créer un produit
        $payload = [
            'name' => 'Test',
            'slug' => 'test',
            'description' => 'Pour update',
            'price' => '10',
            'quantity' => 5,
            'category' => 2,
            'farmer' => 1,
            'illustration' => 'test.jpg'
        ];

        $this->client->request(
            'POST',
            '/api/products',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload)
        );

        self::assertResponseStatusCodeSame(201);
        self::assertResponseFormatSame('json');

        $productData = json_decode($this->client->getResponse()->getContent(), true);
        $id = $productData['id'] ?? null;
        self::assertNotNull($id, 'Le product ID ne doit pas être null après la création.');

        // 2) Update (PUT)
        $updatePayload = [
            'price' => '15',
            'quantity' => 100
        ];
        $this->client->request(
            'PUT',
            '/api/products/' . $id,
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($updatePayload)
        );

        self::assertResponseIsSuccessful();
        self::assertResponseFormatSame('json');

        $updatedData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals('15', $updatedData['price']);
        self::assertEquals(100, $updatedData['quantity']);
    }

    /**
     * Teste la suppression d'un produit : DELETE /api/products/{id}
     */
    public function testDeleteProduct(): void
    {
        // 1) Créer un produit
        $payload = [
            'name' => 'À supprimer',
            'slug' => 'a-supprimer',
            'description' => 'pour test delete',
            'price' => '5',
            'quantity' => 1,
            'category' => 2,
            'farmer' => 1,
            'illustration' => 'remove.jpg'
        ];

        $this->client->request(
            'POST',
            '/api/products',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload)
        );

        self::assertResponseStatusCodeSame(201);
        self::assertResponseFormatSame('json');

        $productData = json_decode($this->client->getResponse()->getContent(), true);
        $id = $productData['id'] ?? null;
        self::assertNotNull($id, 'Le product ID ne doit pas être null après la création.');

        // 2) Supprimer
        $this->client->request('DELETE', '/api/products/' . $id);
        self::assertResponseStatusCodeSame(204);

        // 3) Vérifier qu'on ne le trouve plus (GET -> 404)
        $this->client->request('GET', '/api/products/' . $id);
        self::assertResponseStatusCodeSame(404);
    }
}

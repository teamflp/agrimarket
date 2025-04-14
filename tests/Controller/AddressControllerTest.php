<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AddressControllerTest extends WebTestCase
{
    public function testIndexDisplaysAddressesSuccessfully(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/addresses');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateValidAddressWithApiPlatform(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findOneBy([]); // Récupérer un utilisateur existant

        $client->request('POST', '/api/addresses', [
            'json' => [
                'street' => '123 Main St',
                'city' => 'Sample City',
                'zipCode' => '12345',
                'country' => 'Sample Country',
                'user' => '/api/users/' . $user->getId(),
            ],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCreateAddressWithoutUserFails(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/addresses', [
            'json' => [
                'street' => '123 Main St',
                'city' => 'Sample City',
                'zipCode' => '12345',
                'country' => 'Sample Country',
            ],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testCreateAddressWithInvalidZipCodeFails(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/addresses', [
            'json' => [
                'street' => '123 Main St',
                'city' => 'Sample City',
                'zipCode' => '1234', // Invalid
                'country' => 'Sample Country',
            ],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdateAddressSuccessfully(): void
    {
        $client = static::createClient();
        $userRepository = $client->getContainer()->get('doctrine')->getRepository(User::class);
        $user = $userRepository->findOneBy([]); // Récupérer un utilisateur existant

        $client->request('PUT', '/api/addresses/1', [
            'json' => [
                'street' => '456 Updated St',
                'city' => 'Updated City',
                'zipCode' => '54321',
                'country' => 'Updated Country',
                'user' => '/api/users/' . $user->getId(),
            ],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUpdateAddressWithInvalidDataFails(): void
    {
        $client = static::createClient();
        $client->request('PUT', '/api/addresses/1', [
            'json' => [
                'zipCode' => '1234', // Invalide
            ],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testDeleteAddressAsUnauthorizedUserFails(): void
    {
        $client = static::createClient([], ['HTTP_X-AUTH-TOKEN' => 'invalid-token']);
        $client->request('DELETE', '/api/addresses/1');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testGetAddressById(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/addresses/1');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSearchAddressByCity(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/addresses?city=Sample');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSearchAddressByZipCode(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/addresses?zipCode=123');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateAddressAsUnauthorizedUserFails(): void
    {
        $client = static::createClient([], ['HTTP_X-AUTH-TOKEN' => 'invalid-token']);
        $client->request('POST', '/api/addresses', [
            'json' => [
                'street' => '123 Main St',
                'city' => 'Sample City',
                'zipCode' => '12345',
                'country' => 'Sample Country',
            ],
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
<?php

namespace App\Tests\Service;

use App\Entity\Address;
use App\Entity\User;
use App\Service\AddressService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AddressServiceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private AddressService $addressService;
    private HttpClientInterface $httpClient;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->addressService = $container->get(AddressService::class);
        $this->httpClient = $container->get(HttpClientInterface::class);

        // Nettoyage des données avant chaque test
        $this->entityManager->createQuery('DELETE FROM App\Entity\Address')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
    }

    public function testCreateAddressSuccessfully(): void
    {
        // 1. Créer un utilisateur
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword('password');
        $this->entityManager->persist($user);

        // 2. Créer une adresse
        $address = new Address();
        $address->setStreet('123 Rue Exemple');
        $address->setCity('Paris');
        $address->setZipCode('75001');
        $address->setCountry('France');
        $address->setUser($user);

        $this->entityManager->flush();

        // 3. Appel au service pour créer l'adresse
        $createdAddress = $this->addressService->createAddress($address);

        // 4. Assert
        $this->assertNotNull($createdAddress->getId());
        $this->assertEquals('123 Rue Exemple', $createdAddress->getStreet());
        $this->assertEquals('Paris', $createdAddress->getCity());
        $this->assertEquals('75001', $createdAddress->getZipCode());
        $this->assertEquals('France', $createdAddress->getCountry());
        $this->assertEquals($user->getId(), $createdAddress->getUser()->getId());
        $this->assertNotNull($createdAddress->getLatitude());
        $this->assertNotNull($createdAddress->getLongitude());
    }

    public function testCreateAddressWithInvalidCoordinates(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage("Impossible de récupérer les coordonnées géographiques pour l'adresse fournie.");

        // Créer une adresse sans coordonnées géographiques valides
        $address = new Address();
        $address->setStreet('123 Adresse Invalide');
        $address->setCity('Ville Inexistante');
        $address->setZipCode('99999');
        $address->setCountry('Pays Imaginaire');
        $address->setUser(new User());

        // Appel à la méthode createAddress qui va échouer
        $this->addressService->createAddress($address);
    }

    public function testUpdateAddressSuccessfully(): void
    {
        // Créer un utilisateur et une adresse
        $user = new User();
        $user->setEmail('update@example.com');
        $user->setPassword('password');
        $this->entityManager->persist($user);

        $address = new Address();
        $address->setStreet('Old Street');
        $address->setCity('Old City');
        $address->setZipCode('12345');
        $address->setCountry('Old Country');
        $address->setUser($user);
        $this->entityManager->persist($address);

        $this->entityManager->flush();

        // 2. Créer une nouvelle adresse mise à jour
        $updatedAddress = new Address();
        $updatedAddress->setStreet('New Street');
        $updatedAddress->setCity('New City');
        $updatedAddress->setZipCode('54321');
        $updatedAddress->setCountry('New Country');
        $updatedAddress->setUser($user);

        // Appel au service pour mettre à jour l'adresse
        $updatedAddress = $this->addressService->updateAddress($address->getId(), $updatedAddress);

        // Assert
        $this->assertEquals('New Street', $updatedAddress->getStreet());
        $this->assertEquals('New City', $updatedAddress->getCity());
        $this->assertEquals('54321', $updatedAddress->getZipCode());
        $this->assertEquals('New Country', $updatedAddress->getCountry());
    }

    public function testDeleteAddressSuccessfully(): void
    {
        // Créer un utilisateur et une adresse
        $user = new User();
        $user->setEmail('delete@example.com');
        $user->setPassword('password');
        $this->entityManager->persist($user);

        $address = new Address();
        $address->setStreet('Street to Delete');
        $address->setCity('City to Delete');
        $address->setZipCode('12345');
        $address->setCountry('Country to Delete');
        $address->setUser($user);
        $this->entityManager->persist($address);

        $this->entityManager->flush();

        // Appel au service pour supprimer l'adresse
        $deleted = $this->addressService->deleteAddress($address->getId());

        // Assert
        $this->assertTrue($deleted);
        $this->assertNull($this->entityManager->find(Address::class, $address->getId()));
    }

    public function testDeleteAddressNotFound(): void
    {
        // Essayer de supprimer une adresse qui n'existe pas
        $deleted = $this->addressService->deleteAddress(99999);

        // Assert
        $this->assertFalse($deleted);
    }
}

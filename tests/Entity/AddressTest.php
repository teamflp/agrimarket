<?php

namespace App\Tests\Entity;

use App\Entity\Address;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

//
class AddressTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $address = new Address();

        // Test street
        $address->setStreet('123 Main Street');
        $this->assertSame('123 Main Street', $address->getStreet());

        // Test city
        $address->setCity('Paris');
        $this->assertSame('Paris', $address->getCity());

        // Test zipCode
        $address->setZipCode('75000');
        $this->assertSame('75000', $address->getZipCode());

        // Test country
        $address->setCountry('France');
        $this->assertSame('France', $address->getCountry());

        // Test label
        $address->setLabe('Home');
        $this->assertSame('Home', $address->getLabe());

        // Test latitude
        $address->setLatitude(48.8566);
        $this->assertSame(48.8566, $address->getLatitude());

        // Test longitude
        $address->setLongitude(2.3522);
        $this->assertSame(2.3522, $address->getLongitude());

        // Test user
        $user = new User();
        $address->setUser($user);
        $this->assertSame($user, $address->getUser());
    }
}

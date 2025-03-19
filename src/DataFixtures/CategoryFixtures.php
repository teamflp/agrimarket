<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $categoriesData = ["fruits", "légumes", "viandes"]; // Exemple de données

        $faker = Factory::create("fr_FR");

        foreach (array_keys($categoriesData) as $key) {
            $category = new Category();
            $category->setName($faker->word); // Utilise $faker pour le nom
            $manager->persist($category);
            $this->addReference("category_$key", $category); // Utilise $key
        }
        

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['category'];
    }
}

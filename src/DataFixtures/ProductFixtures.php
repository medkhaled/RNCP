<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $categories = $manager->getRepository(Category::class)->findAll();
        

        for ($i = 0; $i < 100; $i++) {
            $product = new Product();
            $product->setName($faker->word);
            $product->setDescription($faker->paragraph);
            $product->setPrice($faker->randomFloat(2, 5, 500));
            $product->setStock($faker->numberBetween(0, 100));
            $product->setIsVerified($faker->boolean);
           
            $product->setCategory($faker->randomElement($categories));

            $manager->persist($product);
        }

        $manager->flush();
    }
}

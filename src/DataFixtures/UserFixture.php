<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;
class UserFixture extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        
    ){}
    public function load(ObjectManager $manager): void
    {

        $admin = new User();
        $admin->setEmail('admin@ventalis.com');
        $admin->setLastname('Admin');
        $admin->setFirstname('Ventalis');
        $admin->setAddress('12 rue du port');
        $admin->setZipcode('75011');
        $admin->setCity('Paris');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);
        $faker = Faker\Factory::create('fr_FR');
        for($emp = 1; $emp <= 5; $emp++){
            $employee = new User();
            $employee->setEmail($faker->email);
            $employee->setLastname($faker->lastName);
            $employee->setFirstname($faker->firstName);
            $employee->setAddress($faker->streetAddress);
            $employee->setZipcode(str_replace(' ', '', $faker->postcode));
            $employee->setCity($faker->city);
            $employee->setPassword(
                $this->passwordEncoder->hashPassword($employee, 'secret')
            );
            $employee->setRoles(['ROLE_EMPLOYEE']);
            $manager->persist($employee);
        }


       
       
        $manager->flush();
    }
}

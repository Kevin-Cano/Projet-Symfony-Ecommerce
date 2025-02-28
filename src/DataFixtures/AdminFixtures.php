<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail($_ENV['ADMIN_EMAIL']);
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setUserName('Admin');
        $admin->setFirstName('Admin');
        $admin->setLastName('Admin');
        
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            $_ENV['ADMIN_PASSWORD']
        );
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);
        $manager->flush();
    }
} 
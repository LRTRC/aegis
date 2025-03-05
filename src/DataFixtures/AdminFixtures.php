<?php

namespace App\DataFixtures;

use App\Entity\Professional;
use App\Config\ProfessionalPosition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new Professional();
        $admin->setEmail('admin@aegis.fr');
        $admin->setFirstName('Admin');
        $admin->setLastName('AEGIS');
        $admin->setOrganisation('AEGIS');
        $admin->setPosition(ProfessionalPosition::NON_DEFINI);
        $admin->setRoles(['ROLE_ADMIN']);
        
        $password = $this->hasher->hashPassword($admin, 'admin');
        $admin->setPassword($password);

        $manager->persist($admin);
        $manager->flush();
    }
}
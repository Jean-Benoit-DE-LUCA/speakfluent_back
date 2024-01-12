<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // INSERT ROLES //

        $arrayRoles = ['ROLE_ADMIN', 'ROLE_MEMBER'];

        foreach ($arrayRoles as $role) {

            $roleObj = new Role();
            $roleObj->setName($role);
            $manager->persist($roleObj);
        }

        $manager->flush();
    }
}

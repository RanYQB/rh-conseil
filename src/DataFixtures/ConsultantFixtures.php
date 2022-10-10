<?php

namespace App\DataFixtures;

use App\Entity\Consultant;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class ConsultantFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
    ){

    }

    public function load(ObjectManager $manager): void
    {
        for($i = 1; $i <= 3; $i++){
            $user = new User();
            $user->setEmail('consultant'.$i.'@rhconseil.test');
            $user->setRoles(['ROLE_CONSULTANT']);
            $user->setActive(true);
            $user->setPassword(
                $this->userPasswordHasher->hashPassword($user, 'Azerty123'.$i)
            );
            $manager->persist($user);

            $faker = Faker\Factory::create('fr_FR');

            $consultant = new Consultant();
            if($i % 2 == 0){
                $consultant->setCivility('M.');
                $consultant->setFirstname($faker->firstName('male'));
            } else {
                $consultant->setCivility('Mme');
                $consultant->setFirstname($faker->firstName('female'));
            }
            $consultant->setUser($user);
            $consultant->setLastname($faker->lastName());

            $manager->persist($consultant);
        }



        $manager->flush();
    }
}

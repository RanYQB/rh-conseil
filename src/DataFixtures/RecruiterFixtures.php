<?php

namespace App\DataFixtures;

use App\Entity\Recruiter;
use App\Entity\User;
use App\Repository\CityRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RecruiterFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private CityRepository $cityRepository
    ){

    }

    public function load(ObjectManager $manager ): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 1; $i <= 40; $i++){
            $user = new User();
            $user->setEmail($faker->email());
            $user->setRoles(['ROLE_RECRUITER']);
            if($i % 3 == 0){
                $user->setActive(false);
            } else {
                $user->setActive(true);
            }
            $user->setPassword(
                $this->userPasswordHasher->hashPassword($user, 'Azerty123'.$i)
            );
            $manager->persist($user);

            $recruiter = new Recruiter();
            $recruiter->setUser($user);
            $recruiter->setName('Entreprise '.$i);
            $recruiter->setAddress($faker->streetAddress());
            $cities = $this->cityRepository->showBigCities();
            $city = array_rand($cities);
            $recruiter->setCity($cities[$city]);
            $manager->persist($recruiter);
        }

        $manager->flush();
    }
}

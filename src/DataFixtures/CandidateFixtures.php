<?php

namespace App\DataFixtures;

use App\Entity\Candidate;
use App\Entity\Resume;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class CandidateFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
    ){

    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 1; $i <= 70; $i++){
            $user = new User();
            $user->setEmail($faker->email());
            $user->setRoles(['ROLE_CANDIDATE']);
            if($i % 3 == 0){
                $user->setActive(false);
            } else {
                $user->setActive(true);
            }
            $user->setPassword(
                $this->userPasswordHasher->hashPassword($user, 'Azerty123'.$i)
            );
            $manager->persist($user);

            $candidate = new Candidate();
            if($i % 2 == 0){
                $candidate->setCivility('M.');
                $candidate->setFirstname($faker->firstName('male'));
                $titles = ['Assistant comptable', 'Agent d\'accueil', 'Architecte', 'Assistant juridique', 'Assistant commercial',
                    'Aide à domicile', 'Ambulancier', 'Boulanger', 'Cariste', 'Comptable', 'Conducteur de bus', 'Cuisinier', 'Développeur web',
                    'Electricien', 'Infirmier', 'Magasinier', 'Gestionnaire paie', 'Serveur', 'Vendeur'];
                $title = array_rand($titles);
                $candidate->setTitle($titles[$title]);
            } else {
                $candidate->setCivility('Mme');
                $candidate->setFirstname($faker->firstName('female'));
                $titles = ['Assistante comptable', 'Agent d\'accueil', 'Architecte', 'Assistante juridique', 'Assistante commerciale',
                    'Aide à domicile', 'Ambulancière', 'Boulangère', 'Cariste', 'Comptable', 'Conductrice de bus', 'Cuisinière', 'Développeuse web',
                    'Electricienne', 'Infirmière', 'Magasinière', 'Gestionnaire paie', 'Serveuse', 'Vendeuse'];
                $title = array_rand($titles);
                $candidate->setTitle($titles[$title]);
            }
            $candidate->setUser($user);
            $candidate->setLastname($faker->lastName());

            $manager->persist($candidate);

            $resume = new Resume();
            $resume->setCandidate($candidate);
            $resume->setName('CV-'.$candidate->getLastname().'-'.$candidate->getFirstname());
            $resume->setPath('uploads/resumes/Test-63398b305d23c.pdf');
            $manager->persist($resume);

        }

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Offer;
use App\Repository\RecruiterRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecruiterOfferFixtures extends Fixture
{
    public function __construct(
        private RecruiterRepository $recruiterRepository,
        private SluggerInterface $slugger
    ){

    }


    public function load(ObjectManager $manager ): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 1; $i <= 300; $i++){
            $offer = new Offer();
            $recruiters = $this->recruiterRepository->showActive();
            $recruiter = array_rand($recruiters);
            $offer->setRecruiter($recruiters[$recruiter]);
            $titles = ['Assistant comptable', 'Agent d\'accueil', 'Architecte', 'Assistant juridique', 'Assistant commercial',
                'Aide à domicile', 'Ambulancier', 'Boulanger', 'Cariste', 'Comptable', 'Conducteur de bus', 'Cuisinier', 'Développeur web',
                'Electricien', 'Infirmier', 'Magasinier', 'Gestionnaire paie', 'Serveur', 'Vendeur'];
            $title = array_rand($titles);
            $offer->setTitle($titles[$title]);
            $offer->setDescription($faker->text());
            $offer->setSalary(2000);
            if($i % 3 == 0){
                $offer->setContratType('CDD');
            } else {
                $offer->setContratType('CDI');
            }
            $offer->setCity($recruiters[$recruiter]->getCity());
            $offer->setClosed(false);
            $offer->setPositions(rand(1, 3));
            $offer->setSlug($this->slugger->slug($offer->getTitle())->lower());
            if($i % 5 == 0){
                $offer->setPublished(false);
            } else {
                $offer->setPublished(true);
            }
            $offer->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-30 days', '-15 days' )));
            $offer->setPublishedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-14 days', 'now' )));

            $manager->persist($offer);
        }

        $manager->flush();
    }
}

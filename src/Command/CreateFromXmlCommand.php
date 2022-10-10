<?php

namespace App\Command;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'app:create-from-xml',
    description: 'Create from XML file',
)]
class CreateFromXmlCommand extends Command
{
    private SymfonyStyle $io;
    private EntityManagerInterface $entityManager;
    private string $dataDirectory;
    private CityRepository $cityRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $dataDirectory,
        CityRepository $cityRepository,
    )

    {
        parent::__construct();
        $this->dataDirectory = $dataDirectory;
        $this->entityManager = $entityManager;
        $this->cityRepository = $cityRepository;

    }

    protected function configure(): void
    {
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createCities();
        return Command::SUCCESS;
    }

    private function getDataFromFile(): array
    {
        $file = $this->dataDirectory . 'villes_france.xml';

        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        $normalizer = new ObjectNormalizer();

        $encoder = new XmlEncoder();

        $serializer = new Serializer([$normalizer], [$encoder]);

        /**
         * @var string $fileString
         * */
        $fileString = file_get_contents($file);

        $data = $serializer->decode($fileString, $fileExtension);

        return $data["database"]["table"];

    }

    private function createCities(): void
    {

                $this->io->section('Import des données');

                $citiesCreated = 0;

                foreach($this->getDataFromFile() as $row)
                {
                    if( !empty($row["column"][2]["#"])){
                        $city = $this->cityRepository->findOneBy([
                            'label' => $row["column"][2]["#"]
                        ]);


                        if(!$city){
                            $city = new City();
                            $city->setName($row["column"][5]["#"]);
                            $city->setLabel($row["column"][2]["#"]);
                            $city->setZipcode($row["column"][8]["#"]);
                            $city->setLatitude($row["column"][20]["#"]);
                            $city->setLongitude($row["column"][19]["#"]);
                            $city->setDepartmentNumber($row["column"][1]["#"]);
                            $city->setPopulation($row["column"][14]["#"]);

                            $this->entityManager->persist($city);

                            $citiesCreated++;
                        }
                    }

                }
                $this->entityManager->flush();
                $this->io->success("{$citiesCreated} VILLES CREES.");

    }
}

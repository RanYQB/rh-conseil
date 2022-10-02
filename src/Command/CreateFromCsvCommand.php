<?php

namespace App\Command;

use App\Entity\City;
use App\Entity\Department;
use App\Entity\Region;
use App\Repository\CityRepository;
use App\Repository\DepartmentRepository;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[AsCommand(
    name: 'app:create-from-csv',
    description: 'Create from CSV file',
)]
class CreateFromCsvCommand extends Command
{
    private SymfonyStyle $io;
    private EntityManagerInterface $entityManager;
    private string $dataDirectory;
    private CityRepository $cityRepository;
    private DepartmentRepository $departmentRepository;
    private RegionRepository $regionRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $dataDirectory,
        CityRepository $cityRepository,
        DepartmentRepository $departmentRepository,
        RegionRepository $regionRepository
    )
    {
        parent::__construct();
        $this->dataDirectory = $dataDirectory;
        $this->entityManager = $entityManager;
        $this->cityRepository = $cityRepository;
        $this->departmentRepository = $departmentRepository;
        $this->regionRepository = $regionRepository;
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
        $file = $this->dataDirectory . 'cities.csv';

        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        $normalizer = new ObjectNormalizer();

        $encoder = new CsvEncoder();

        $serializer = new Serializer([$normalizer], [$encoder]);

        /**
         * @var string $fileString
         * */
        $fileString = file_get_contents($file);

        $data = $serializer->decode($fileString, $fileExtension);

        return $data;

    }

    private function createCities(): void
    {
        $this->io->section('Import des données');

        $citiesCreated = 0;
        $departmentsCreated = 0;
        $regionsCreated = 0;

        foreach($this->getDataFromFile() as $row)
        {
            if(array_key_exists('insee_code', $row) && !empty($row['insee_code'])){
                $city = $this->cityRepository->findOneBy([
                    'insee_code' => $row['insee_code']
                ]);

                $department = $this->departmentRepository->findOneBy([
                    'number' => $row['department_number']
                ]);

                $region = $this->regionRepository->findOneBy([
                    'name' => $row['region_geojson_name']
                ]);

                if(!$region){
                    $region = new Region();
                    $region->setName($row['region_geojson_name']);

                    $this->entityManager->persist($region);

                    $regionsCreated++;
                }

                if(!$department){
                    $department = new Department();
                    $department->setName($row['department_name']);
                    $department->setNumber($row['department_number']);
                    $department->setRegion($region);

                    $this->entityManager->persist($department);

                    $departmentsCreated++;

                }

                if(!$city){
                    $city = new City();
                    $city->setName($row['city_code']);
                    $city->setInseeCode($row['insee_code']);
                    $city->setLabel($row['label']);
                    $city->setZipcode($row['zip_code']);
                    $city->setLatitude($row['latitude']);
                    $city->setLongitude($row['longitude']);
                    $city->setDepartment($department);

                    $this->entityManager->persist($city);

                    $citiesCreated++;
                }
            }

            $this->entityManager->flush();

        }

        $this->io->success("{$regionsCreated} REGIONS, {$departmentsCreated} DEPARTEMENTS ET {$citiesCreated} VILLES CREES.");
    }
}

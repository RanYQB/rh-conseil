<?php

namespace App\Repository;

use App\Entity\Recruiter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recruiter>
 *
 * @method Recruiter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recruiter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recruiter[]    findAll()
 * @method Recruiter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecruiterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recruiter::class);
    }

    public function add(Recruiter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recruiter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function showPending(){
        $query = $this->createQueryBuilder('r');

        $query->select('r', 'u')
            ->innerJoin('r.user', 'u')
            ->where('u.active = 0')
            ->orderBy('r.name');

        return $query->getQuery()->getResult();
    }

    public function showActive(){
        $query = $this->createQueryBuilder('r');

        $query->select('r', 'u')
            ->innerJoin('r.user', 'u')
            ->where('u.active = 1')
            ->orderBy('r.name');

        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Recruiter[] Returns an array of Recruiter objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Recruiter
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

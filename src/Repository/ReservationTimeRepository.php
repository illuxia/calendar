<?php

namespace App\Repository;

use App\Entity\ReservationTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReservationTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReservationTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReservationTime[]    findAll()
 * @method ReservationTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationTime::class);
    }

    // /**
    //  * @return ReservationTime[] Returns an array of ReservationTime objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReservationTime
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

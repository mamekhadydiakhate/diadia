<?php

namespace App\Repository;

use DateTime;
use App\Entity\Difficulte;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Difficulte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Difficulte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Difficulte[]    findAll()
 * @method Difficulte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DifficulteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Difficulte::class);
    }

    // /**
    //  * @return Difficulte[] Returns an array of Difficulte objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Difficulte
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function precede($semaine ,$year)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.semaine = :semaine')
            ->setParameter('semaine', $semaine)
           // ->andWhere('a.createdAt BETWEEN :start AND :end')
           // ->setParameter('start',new DateTime("$year-01-01"))
            //->setParameter('end',new DateTime("$year-12-31"))
            ->getQuery()
            ->getResult()
            
        ;
    }
}

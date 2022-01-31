<?php

namespace App\Repository;

use App\Entity\Structure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Structure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Structure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Structure[]    findAll()
 * @method Structure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StructureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Structure::class);
    }

    // /**
    //  * @return Structure[] Returns an array of Structure objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Structure
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function goToWeek(int $structure, int $semaine):array{

        $_em = $this->getEntityManager();
        $query = $_em->createQuery('SELECT a FROM App\Entity\Activite a WHERE a.structure = :structure AND a.semaine = :semaine')->
        setParameter("structure", $structure)->setParameter("semaine", $semaine);

        return $query->getResult();
    }

    public function goToWeekEvent(int $structure, int $semaine):array{

        $_em = $this->getEntityManager();
        $query = $_em->createQuery('SELECT a FROM App\Entity\Evenement a WHERE a.structure = :structure AND a.semaine = :semaine')->
        setParameter("structure", $structure)->setParameter("semaine", $semaine);

        return $query->getResult();
    }

    public function goToMoisEvent(int $structure, int $mois):array{

        $_em = $this->getEntityManager();
        $query = $_em->createQuery('SELECT a FROM App\Entity\Evenement a WHERE a.structure = :structure AND a.mois = :mois')->
        setParameter("structure", $structure)->setParameter("mois", $mois);

        return $query->getResult();
    }

    public function goToWeekDiff(int $structure, int $semaine):array{

        $_em = $this->getEntityManager();
        $query = $_em->createQuery('SELECT a FROM App\Entity\Difficulte a WHERE a.structure = :structure AND a.semaine = :semaine')->
        setParameter("structure", $structure)->setParameter("semaine", $semaine);

        return $query->getResult();
    }
        
    public function precede(int $semaine)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.structure.activite.semaine = :semaine')
            ->setParameter('semaine', $semaine)
            ->getQuery()
            ->getResult()
            
        ;
    }
}

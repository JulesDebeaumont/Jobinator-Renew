<?php

namespace App\Repository;

use App\Entity\JobImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method JobImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobImage[]    findAll()
 * @method JobImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobImage::class);
    }

    // /**
    //  * @return JobImage[] Returns an array of JobImage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?JobImage
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

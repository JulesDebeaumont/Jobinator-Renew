<?php

namespace App\Repository;

use App\Entity\FileApplication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FileApplication|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileApplication|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileApplication[]    findAll()
 * @method FileApplication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileApplication::class);
    }

    // /**
    //  * @return FileApplication[] Returns an array of FileApplication objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?File
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

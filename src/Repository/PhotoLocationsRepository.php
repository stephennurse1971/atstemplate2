<?php

namespace App\Repository;

use App\Entity\PhotoLocations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PhotoLocations|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhotoLocations|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhotoLocations[]    findAll()
 * @method PhotoLocations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoLocationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhotoLocations::class);
    }

    // /**
    //  * @return PhotoLocations[] Returns an array of PhotoLocations objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PhotoLocations
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getUniqueGroupId(){
        return $this->createQueryBuilder('p')
           ->select('p.groupId')
            ->distinct()
            ->getQuery()
            ->getResult()
            ;
    }
}

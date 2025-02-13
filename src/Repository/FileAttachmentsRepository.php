<?php

namespace App\Repository;

use App\Entity\FileAttachments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FileAttachments|null find($id, $lockMode = null, $lockVersion = null)
 * @method FileAttachments|null findOneBy(array $criteria, array $orderBy = null)
 * @method FileAttachments[]    findAll()
 * @method FileAttachments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileAttachmentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FileAttachments::class);
    }

    // /**
    //  * @return FileAttachments[] Returns an array of FileAttachments objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FileAttachments
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

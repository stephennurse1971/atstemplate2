<?php

namespace App\Repository;

use App\Entity\BusinessContacts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BusinessContacts>
 *
 * @method BusinessContacts|null find($id, $lockMode = null, $lockVersion = null)
 * @method BusinessContacts|null findOneBy(array $criteria, array $orderBy = null)
 * @method BusinessContacts[]    findAll()
 * @method BusinessContacts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BusinessContactsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BusinessContacts::class);
    }

    public function add(BusinessContacts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BusinessContacts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countByBusinessType($businessType)
    {
        return $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.businessType = :businessType')
            ->setParameter('businessType', $businessType)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByBusinessTypeAndStatus($businessType, $status = 'Approved')
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.businessType = :businessType')
            ->setParameter('businessType', $businessType)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByBusinessTypeAndStatusAndMapLocation($businessType, $status = 'Approved')
    {
        $qb = $this->createQueryBuilder('bc');
        $qb->select('COUNT(bc.id)')
            ->where('bc.businessType = :businessType')
            ->andWhere('bc.status = :status')
            ->andWhere('bc.locationLatitude != 0')
            ->setParameter('businessType', $businessType)
            ->setParameter('status', $status);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }


//    /**
//     * @return BusinessContacts[] Returns an array of BusinessContacts objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BusinessContacts
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

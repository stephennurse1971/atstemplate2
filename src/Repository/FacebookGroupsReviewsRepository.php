<?php

namespace App\Repository;

use App\Entity\FacebookGroupsReviews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FacebookGroupsReviews>
 *
 * @method FacebookGroupsReviews|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacebookGroupsReviews|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacebookGroupsReviews[]    findAll()
 * @method FacebookGroupsReviews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacebookGroupsReviewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacebookGroupsReviews::class);
    }

    public function add(FacebookGroupsReviews $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FacebookGroupsReviews $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findByDateLatest(): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return FacebookGroupsReviews[] Returns an array of FacebookGroupsReviews objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FacebookGroupsReviews
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

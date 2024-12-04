<?php

namespace App\Repository;

use App\Entity\CmsCopyPageFormats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CmsCopyPageFormats>
 *
 * @method CmsCopyPageFormats|null find($id, $lockMode = null, $lockVersion = null)
 * @method CmsCopyPageFormats|null findOneBy(array $criteria, array $orderBy = null)
 * @method CmsCopyPageFormats[]    findAll()
 * @method CmsCopyPageFormats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CmsCopyPageFormatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CmsCopyPageFormats::class);
    }

    public function add(CmsCopyPageFormats $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CmsCopyPageFormats $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CmsCopyPageFormats[] Returns an array of CmsCopyPageFormats objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CmsCopyPageFormats
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository;

use App\Entity\Languages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Languages>
 *
 * @method Languages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Languages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Languages[]    findAll()
 * @method Languages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanguagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Languages::class);
    }

    public function add(Languages $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Languages $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Languages[] Returns an array of LanguagesService objects
     */
    public function getLanguagesByRankingOrder(): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.isActive = true')
            ->orderBy('l.ranking', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?LanguagesService
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

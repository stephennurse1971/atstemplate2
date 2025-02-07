<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        // Update the user's password
        $user->setPassword($newHashedPassword);

        // Persist the changes in the database
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function User()
    {
        return $this->createQueryBuilder('t')
            ->select('t.fullname')
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    public function lastEditedListByDate(\DateTimeInterface $date)
    {
        return $this->createQueryBuilder('u')
            ->Where('u.lastEdited > :val')
            ->setParameter('val', $date)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $role
     * @return array
     */
    public function findByRole($role)
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%')
            ->orderBy('u.fullName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCompany($company)
    {
        return $this->createQueryBuilder('u')
            ->where('u.company LIKE :company')
            ->setParameter('company', $company.'%')
            ->getQuery()
            ->getResult();
    }

    public function findByBirthday($birthday)
    {
        return $this->createQueryBuilder('u')
            ->where('u.birthday LIKE :company')
            ->setParameter('birthday', '1')
            ->getQuery()
            ->getResult();
    }
}

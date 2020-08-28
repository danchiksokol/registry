<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }


    public function findByAllFields($search)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->where("u.firstname like :query")
            ->orWhere("u.middlename like :query")
            ->orWhere("u.lastname like :query")
            ->orWhere("u.job like :query")
            ->orWhere("u.position like :query")
            ->orWhere("u.phone like :query")
            ->orWhere("u.email like :query")
            ->orWhere("u.city like :query")
            ->orWhere("u.country like :query")
            ->orderBy('u.id', "ASC")
            ->setParameter('query', '%'.$search.'%');

        return $qb->getQuery()->getResult();
    }
    // /**
    //  * @return Users[] Returns an array of Users objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Users
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

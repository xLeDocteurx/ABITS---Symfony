<?php

namespace App\Repository;

use App\Entity\BottlesSent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BottlesSent|null find($id, $lockMode = null, $lockVersion = null)
 * @method BottlesSent|null findOneBy(array $criteria, array $orderBy = null)
 * @method BottlesSent[]    findAll()
 * @method BottlesSent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BottlesSentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BottlesSent::class);
    }

//    /**
//     * @return BottlesSent[] Returns an array of BottlesSent objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BottlesSent
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

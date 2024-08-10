<?php

namespace App\Repository;

use App\Entity\Baniere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Baniere>
 *
 * @method Baniere|null find($id, $lockMode = null, $lockVersion = null)
 * @method Baniere|null findOneBy(array $criteria, array $orderBy = null)
 * @method Baniere[]    findAll()
 * @method Baniere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BaniereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Baniere::class);
    }

//    /**
//     * @return Baniere[] Returns an array of Baniere objects
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

//    public function findOneBySomeField($value): ?Baniere
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

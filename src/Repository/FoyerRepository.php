<?php

namespace App\Repository;

use App\Entity\Foyer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Foyer>
 */
class FoyerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Foyer::class);
    }

    /**
     * Trouver les foyers qui ont encore des chambres disponibles.
     *
     * @return Foyer[]
     */
    public function findAvailableFoyers(): array
{
    return $this->createQueryBuilder('f')
        ->leftJoin('f.chambres', 'c')
        ->addSelect('f') // Add the Foyer entity to the result
        ->addSelect('COUNT(c.id) AS HIDDEN totalChambres') // Count total chambres
        ->addSelect('SUM(CASE WHEN c.type = :single THEN 1 ELSE 0 END) AS HIDDEN singleCount') // Sum single chambres
        ->addSelect('SUM(CASE WHEN c.type = :double THEN 1 ELSE 0 END) AS HIDDEN doubleCount') // Sum double chambres
        ->groupBy('f.id')
        ->having('singleCount < f.nombreChambresSingle')
        ->orHaving('doubleCount < f.nombreChambresDouble')
        ->setParameter('single', 'single')
        ->setParameter('double', 'double')
        ->getQuery()
        ->getResult();
}

    
//    /**
//     * @return Foyer[] Returns an array of Foyer objects
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

//    public function findOneBySomeField($value): ?Foyer
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

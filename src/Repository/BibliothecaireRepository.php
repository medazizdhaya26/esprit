<?php

namespace App\Repository;

use App\Entity\Bibliothecaire;
use Doctrine\ORM\EntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bibliothecaire>
 */
class BibliothecaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bibliothecaire::class);
    }

    // Vous pouvez ajouter vos méthodes personnalisées ici si nécessaire

//    /**
//     * @return Bibliothecaire[] Returns an array of Bibliothecaire objects
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

//    public function findOneBySomeField($value): ?Bibliothecaire
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findAllSortedByNameAndPrenom(string $order = 'ASC'): array
{
    return $this->createQueryBuilder('b')
        ->orderBy('b.nom', $order)
        ->addOrderBy('b.prenom', $order)
        ->getQuery()
        ->getResult();
}

public function findByFilters($specialisation = null, $bibliotheque = null)
{
    $qb = $this->createQueryBuilder('b');

    if ($specialisation) {
        $qb->andWhere('b.specialisation = :specialisation')
           ->setParameter('specialisation', $specialisation);
    }

    if ($bibliotheque) {
        $qb->andWhere('b.bibliotheque = :bibliotheque')
           ->setParameter('bibliotheque', $bibliotheque);
    }

    return $qb->getQuery()->getResult();
}
}
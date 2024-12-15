<?php

namespace App\Repository;

use App\Entity\Entraineur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Entraineur>
 */
class EntraineurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entraineur::class);
    }

//    /**
//     * @return Entraineur[] Returns an array of Entraineur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Entraineur
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
// src/Repository/EntraineurRepository.php

public function searchBySpecialiteEtSalle(string $specialite, string $salle): array
{
    $qb = $this->createQueryBuilder('e');

    // Filtrer par spécialité, si spécifiée
    if ($specialite) {
        $qb->andWhere('e.specialite LIKE :specialite')
           ->setParameter('specialite', '%' . $specialite . '%');
    }

    // Filtrer par salle, si spécifiée
    if ($salle) {
        $qb->leftJoin('e.salle', 's')
           ->andWhere('s.nom LIKE :salle')
           ->setParameter('salle', '%' . $salle . '%');
    }

    return $qb->getQuery()->getResult();
}



}

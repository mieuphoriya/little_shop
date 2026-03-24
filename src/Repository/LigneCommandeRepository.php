<?php

namespace App\Repository;

use App\Entity\LigneCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LigneCommande>
 */
class LigneCommandeRepository extends ServiceEntityRepository
{
public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneCommande::class);
    }

//    public function findBestSellers(int $max): array
//    {
//        $entityManager = $this->getEntityManager();
//
//        $query = $entityManager->createQuery(
//            'SELECT p.id AS produit_id, SUM(cl.quantite) AS total_quantite
//         FROM App\Entity\LigneCommande cl
//         JOIN cl.produit p
//         GROUP BY p.id
//         ORDER BY total_quantite DESC'
//        )->setMaxResults($max);
//
//        return $query->getResult();
//    }

}




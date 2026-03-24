<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }
//on acess aux methedes de base findby id find all
//si on a besoin de methedes particuliers on le code un select particulier

    //    /**
    //     * @return Produit[] Returns an array of Produit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

        public function findProduits(string $libelle): array
        {
            $entityManager = $this->getEntityManager();
            $query = $entityManager->createQuery(
                    'SELECT p
                    FROM App\Entity\Produit p
                    WHERE p.libelle LIKE :libelle')->setParameter('libelle', '%' . $libelle . '%') ;

        return $query->getResult();
                }

// select produit_id, sum(quantite) from ligne_commande group by produit_id;
    public function findBestSellers(int $max): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT p.id AS produitId, SUM(cl.quantite) AS total
         FROM App\Entity\LigneCommande cl
         JOIN cl.produit p
         GROUP BY p.id
         ORDER BY total DESC'
        )->setMaxResults($max);

        return $query->getResult();
    }
}

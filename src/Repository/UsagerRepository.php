<?php

namespace App\Repository;
use App\Entity\Usager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Usager>
 */
class UsagerRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usager::class);
    }
}


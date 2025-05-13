<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    /**
     * Returns the top 10 most viewed news articles.
     *
     * @return News[]
     */
    public function findTopTenNews(): array
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.views', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}

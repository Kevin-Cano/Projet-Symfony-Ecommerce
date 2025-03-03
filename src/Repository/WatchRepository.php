<?php

namespace App\Repository;

use App\Entity\Watch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Watch>
 */
class WatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Watch::class);
    }

    //    /**
    //     * @return Watch[] Returns an array of Watch objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Watch
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findSixMostExpensive(): array
    {
        return $this->createQueryBuilder('w')
            ->orderBy('w.price', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    public function findSixOtherWatches(): array
    {
        return $this->createQueryBuilder('w')
            ->orderBy('w.id', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Watch[] Returns an array of shop watches
     */
    public function findShopWatches(): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.author IS NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Watch[] Returns a limited number of private watches
     */
    public function findPrivateWatches(int $limit = 3): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.author IS NOT NULL')
            ->setMaxResults($limit)
            ->orderBy('w.publicationDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function searchWatches(string $query)
    {
        $qb = $this->createQueryBuilder('w')
            ->leftJoin('w.stock', 's')
            ->addSelect('s')
            ->leftJoin('w.author', 'a')
            ->addSelect('a')
            ->where('w.name LIKE :query')
            ->orWhere('w.reference LIKE :query')
            ->orWhere('w.description LIKE :query')
            ->orWhere('w.movement LIKE :query')
            ->orWhere('w.material LIKE :query')
            ->orWhere('w.bracelet LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('w.createdAt', 'DESC');
        
        return $qb->getQuery()->getResult();
    }
}

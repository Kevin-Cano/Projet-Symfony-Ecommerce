<?php

namespace App\Repository;

use App\Entity\Favorite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favorite>
 */
class FavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favorite::class);
    }

    public function findByUser($user): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user)
            ->leftJoin('f.watch', 'watch')
            ->addSelect('watch')
            ->leftJoin('watch.stock', 'stock')
            ->addSelect('stock')
            ->orderBy('f.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByUserAndWatch($user, $watch): ?Favorite
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.user = :user')
            ->andWhere('f.watch = :watch')
            ->setParameter('user', $user)
            ->setParameter('watch', $watch)
            ->getQuery()
            ->getOneOrNullResult();
    }
} 
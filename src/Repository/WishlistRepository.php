<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Wishlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wishlist>
 */
class WishlistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wishlist::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :user')
            ->setParameter('user', $user)
            ->leftJoin('w.watch', 'watch')
            ->addSelect('watch')
            ->leftJoin('watch.stock', 'stock')
            ->addSelect('stock')
            ->orderBy('w.addedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByUserAndWatch(User $user, int $watchId): ?Wishlist
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.user = :user')
            ->andWhere('w.watch = :watchId')
            ->setParameter('user', $user)
            ->setParameter('watchId', $watchId)
            ->getQuery()
            ->getOneOrNullResult();
    }
} 
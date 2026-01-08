<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function countUpvotes(int $id): int
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->select('COUNT(u.id)')
            ->leftJoin('p.upvotes', 'u')
            ->where('p.id = :id')
            ->setParameter('id', $id)
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }
}

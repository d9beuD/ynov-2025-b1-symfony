<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\CommentUpvote;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentUpvote>
 */
class CommentUpvoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentUpvote::class);
    }

    public function findPost(Comment $comment): Post
    {
        return $comment->getPost() ? $comment->getPost() : $this->findPost($comment->getParent());
    }
}

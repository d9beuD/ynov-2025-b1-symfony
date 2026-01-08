<?php

namespace App\Service;

use App\Entity\Comment;
use App\Entity\CommentDownvote;
use App\Entity\CommentUpvote;
use App\Entity\User;
use App\Repository\CommentDownvoteRepository;
use App\Repository\CommentRepository;
use App\Repository\CommentUpvoteRepository;
use Doctrine\ORM\EntityManagerInterface;

class CommentManager
{
    public function __construct(
        protected CommentDownvoteRepository $commentDownvoteRepository,
        protected CommentRepository $commentRepository,
        protected CommentUpvoteRepository $commentUpvoteRepository,
        protected EntityManagerInterface $entityManager,
    ) {}

    public function upvote(Comment $comment, User $user, bool $flush = false): void
    {
        $upvote = $this->commentUpvoteRepository->findOneBy(['comment' => $comment, 'user' => $user]);
        $downvote = $this->commentDownvoteRepository->findOneBy(['comment' => $comment, 'user' => $user]);

        if ($downvote !== null) {
            $this->entityManager->remove($downvote);
        }

        if ($upvote !== null) {
            $this->entityManager->remove($upvote);
        } else {
            $commentUpvote = new CommentUpvote();
            $commentUpvote
                ->setComment($comment)
                ->setUser($user)
            ;
    
            $this->entityManager->persist($commentUpvote);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function downvote(Comment $comment, User $user, bool $flush = false): void
    {
        $upvote = $this->commentUpvoteRepository->findOneBy(['comment' => $comment, 'user' => $user]);
        $downvote = $this->commentDownvoteRepository->findOneBy(['comment' => $comment, 'user' => $user]);

        if ($upvote !== null) {
            $this->entityManager->remove($upvote);
        }
        
        if ($downvote !== null) {
            $this->entityManager->remove($downvote);
        } else {
            $commentDownvote = new CommentDownvote();
            $commentDownvote
                ->setComment($comment)
                ->setUser($user)
            ;

            $this->entityManager->persist($commentDownvote);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}

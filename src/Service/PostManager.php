<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\PostDownvote;
use App\Entity\PostUpvote;
use App\Entity\User;
use App\Repository\PostDownvoteRepository;
use App\Repository\PostUpvoteRepository;
use Doctrine\ORM\EntityManagerInterface;

class PostManager
{
    public function __construct(
        protected PostUpvoteRepository $postUpvoteRepository,
        protected PostDownvoteRepository $postDownvoteRepository,
        protected EntityManagerInterface $entityManager,
    ) {}

    public function upvote(Post $post, User $user, bool $flush = false): void
    {
        $downvote = $this->postDownvoteRepository->findOneBy(['user' => $user, 'post' => $post]);

        if ($downvote !== null) {
            $this->entityManager->remove($downvote);
        }

        $upvote = $this->postUpvoteRepository->findOneBy(['post' => $post, 'user' => $user]);

        if ($upvote !== null) {
            $this->entityManager->remove($upvote);
        } else {
            $postUpvote = new PostUpvote();
            $postUpvote
                ->setPost($post)
                ->setUser($user)
            ;
    
            $this->entityManager->persist($postUpvote);
        }
     
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function downvote(Post $post, User $user, bool $flush = false): void
    {
        $upvote = $this->postUpvoteRepository->findOneBy(['user' => $user, 'post' => $post]);

        if ($upvote !== null) {
            $this->entityManager->remove($upvote);
        }

        $downvote = $this->postDownvoteRepository->findOneBy(['post' => $post, 'user' => $user]);

        if ($downvote !== null) {
            $this->entityManager->remove($downvote);
        } else {
            $postDownvote = new PostDownvote();
            $postDownvote
                ->setPost($post)
                ->setUser($user)
            ;
    
            $this->entityManager->persist($postDownvote);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}

<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentDownvote;
use App\Entity\CommentUpvote;
use App\Entity\User;
use App\Repository\CommentDownvoteRepository;
use App\Repository\CommentRepository;
use App\Repository\CommentUpvoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class CommentController extends AbstractController
{
    #[Route('/comment/{id}/upvote', name: 'app_comment_upvote', methods: ['GET'])]
    public function upvote(
        #[CurrentUser()] User $user,
        EntityManagerInterface $entityManager,
        Comment $comment,
        CommentUpvoteRepository $commentUpvoteRepository,
        CommentDownvoteRepository $commentDownvoteRepository,
        CommentRepository $commentRepository,
    ): Response
    {
        $post = $commentRepository->findPost($comment);
        $upvote = $commentUpvoteRepository->findOneBy(['comment' => $comment, 'user' => $user]);
        $downvote = $commentDownvoteRepository->findOneBy(['comment' => $comment, 'user' => $user]);

        if ($downvote !== null) {
            $entityManager->remove($downvote);
        }

        if ($upvote !== null) {
            $entityManager->remove($upvote);
        } else {
            $commentUpvote = new CommentUpvote();
            $commentUpvote
                ->setComment($comment)
                ->setUser($user)
            ;
    
            $entityManager->persist($commentUpvote);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
    }

    #[Route('/comment/{id}/downvote', name: 'app_comment_downvote', methods: ['GET'])]
    public function downvote(
        #[CurrentUser()] User $user,
        EntityManagerInterface $entityManager,
        Comment $comment,
        CommentRepository $commentRepository,
        CommentDownvoteRepository $commentDownvoteRepository,
        CommentUpvoteRepository $commentUpvoteRepository,
    ): Response {
        $post = $commentRepository->findPost($comment);
        $upvote = $commentUpvoteRepository->findOneBy(['comment' => $comment, 'user' => $user]);
        $downvote = $commentDownvoteRepository->findOneBy(['comment' => $comment, 'user' => $user]);

        if ($upvote !== null) {
            $entityManager->remove($upvote);
        }
        
        if ($downvote !== null) {
            $entityManager->remove($downvote);
        } else {
            $commentDownvote = new CommentDownvote();
            $commentDownvote
                ->setComment($comment)
                ->setUser($user)
            ;

            $entityManager->persist($commentDownvote);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
    }
}

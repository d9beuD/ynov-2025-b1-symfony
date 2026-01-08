<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentUpvote;
use App\Entity\User;
use App\Repository\CommentUpvoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class CommentController extends AbstractController
{
    #[Route('/comment/{id}', name: 'app_comment_upvote', methods: ['GET'])]
    public function upvote(
        #[CurrentUser()] User $user,
        EntityManagerInterface $entityManager,
        Comment $comment,
        CommentUpvoteRepository $commentUpvoteRepository,
    ): Response
    {
        $post = $commentUpvoteRepository->findPost($comment);
        $upvote = $commentUpvoteRepository->findOneBy(['comment' => $comment, 'user' => $user]);

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
}

<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\PostUpvote;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post, PostRepository $postRepository): Response
    {
        $comment = new Comment();
        $comment->setAuthor($this->getUser());

        $action = $this->generateUrl('app_post_comment_new', ['id' => $post->getId()]);
        $form = $this->createForm(CommentType::class, $comment, ['action' => $action]);

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'upvotesCount' => $postRepository->countUpvotes($post->getId()),
            'form' => $form,
        ]);
    }

    #[Route('/{id}/comment/new', name:'app_post_comment_new', methods: ['POST'])]
    public function comment(Post $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setAuthor($this->getUser());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_show', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_post_show', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_show', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/upvote', name: 'app_post_upvote', methods: ['GET'])]
    public function upvote(#[CurrentUser()] User $user, Post $post, EntityManagerInterface $entityManager): Response
    {
        $postUpvote = new PostUpvote();
        $postUpvote
            ->setPost($post)
            ->setUser($user)
        ;

        $entityManager->persist($postUpvote);
        $entityManager->flush();

        return $this->redirectToRoute('app_post_show', ['id' => $post->getId()]);
    }
}

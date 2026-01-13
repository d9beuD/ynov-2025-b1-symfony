<?php

namespace App\Twig\Components;

use App\Entity\Post as EntityPost;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Service\PostManager;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Post
{
    use DefaultActionTrait;
    
    public EntityPost $post;

    #[LiveAction]
    public function upvote(
        PostManager $postManager,
        PostRepository $postRepository,
        #[LiveArg()] int $id,
        #[CurrentUser()] User $user,
    ): void
    {
        $post = $postRepository->find($id);
        $postManager->upvote($post, $user, flush: true);
    }

    #[LiveAction]
    public function downvote(
        PostManager $postManager,
        PostRepository $postRepository,
        #[LiveArg()] int $id,
        #[CurrentUser()] User $user,
    ): void
    {
        $post = $postRepository->find($id);
        $postManager->downvote($post, $user, flush: true);
    }
}

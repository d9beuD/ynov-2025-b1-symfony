<?php

namespace App\Twig\Components\Post;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostDownvoteRepository;
use App\Repository\PostRepository;
use App\Repository\PostUpvoteRepository;
use App\Service\PostManager;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ActionsVote
{
    use DefaultActionTrait;

    public Post $post;

    public function __construct(
        protected PostRepository $postRepository,
        protected PostUpvoteRepository $postUpvoteRepository,
        protected PostDownvoteRepository $postDownvoteRepository,
        protected Security $security,
    ) {}

    public function getUpvoteCount(): int
    {
        return $this->postRepository->countUpvotes($this->post->getId());
    }

    public function getDownvoteCount(): int
    {
        return $this->postRepository->countDownvotes($this->post->getId());
    }

    public function hasUpvoted(): bool
    {
        return $this->postUpvoteRepository->findOneBy([
            'post' => $this->post,
            'user' => $this->security->getUser(),
        ]) !== null;
    }

    public function hasDownvoted(): bool
    {
        return $this->postDownvoteRepository->findOneBy([
            'post' => $this->post,
            'user' => $this->security->getUser(),
        ]) !== null;
    }
}

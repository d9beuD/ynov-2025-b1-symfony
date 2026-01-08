<?php

namespace App\Twig\Components;

use App\Entity\Comment as EntityComment;
use App\Entity\User;
use App\Repository\CommentDownvoteRepository;
use App\Repository\CommentUpvoteRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Comment
{
    use DefaultActionTrait;

    public EntityComment $comment;

    public function __construct(
        protected CommentUpvoteRepository $commentUpvoteRepository,
        protected CommentDownvoteRepository $commentDownvoteRepository,
        protected Security $security,
    ) {}

    public function countUpvotes(): int
    {
        return $this->commentUpvoteRepository->count([
            'comment' => $this->comment,
            'user' => $this->security->getUser(),
        ]);
    }

    public function countDownvotes(): int
    {
        return $this->commentDownvoteRepository->count([
            'comment' => $this->comment,
            'user' => $this->security->getUser(),
        ]);
    }

    public function countVotes(): int
    {
        return $this->countUpvotes() - $this->countDownvotes();
    }
}

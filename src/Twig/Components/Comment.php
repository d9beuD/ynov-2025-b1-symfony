<?php

namespace App\Twig\Components;

use App\Entity\Comment as EntityComment;
use App\Entity\User;
use App\Repository\CommentDownvoteRepository;
use App\Repository\CommentRepository;
use App\Repository\CommentUpvoteRepository;
use App\Service\CommentManager;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Comment
{
    use DefaultActionTrait;

    #[LiveProp]
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

    public function userHasUpvoted(): bool
    {
        return $this->commentUpvoteRepository->findOneBy([
            'comment' => $this->comment,
            'user' => $this->security->getUser()
        ]) !== null;
    }

    public function userHasDownvoted(): bool
    {
        return $this->commentDownvoteRepository->findOneBy([
            'comment' => $this->comment,
            'user' => $this->security->getUser()
        ]) !== null;
    }

    #[LiveAction]
    public function upvote(
        CommentManager $commentManager,
        CommentRepository $commentRepository,
        #[LiveArg()] int $commentId,
        #[CurrentUser()] User $user,
    ): void {
        $comment = $commentRepository->find($commentId);
        $commentManager->upvote($comment, $user, true);
    }

    #[LiveAction]
    public function downvote(
        CommentManager $commentManager,
        CommentRepository $commentRepository,
        #[LiveArg()] int $commentId,
        #[CurrentUser()] User $user,
    ): void {
        $comment = $commentRepository->find($commentId);
        $commentManager->downvote($comment, $user, true);
    }
}

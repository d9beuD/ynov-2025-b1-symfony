<?php

namespace App\Twig\Components\Post;

use App\Entity\Post;
use App\Repository\PostDownvoteRepository;
use App\Repository\PostRepository;
use App\Repository\PostUpvoteRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Actions
{
    use DefaultActionTrait;

    #[LiveProp()]
    public Post $post;
}

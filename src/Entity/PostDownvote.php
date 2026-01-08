<?php

namespace App\Entity;

use App\Repository\PostDownvoteRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostDownvoteRepository::class)]
#[ORM\UniqueConstraint('UNIQUE_POST_USER', fields:['post', 'user'])]
#[ORM\HasLifecycleCallbacks]
class PostDownvote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'downvotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post = null;

    #[ORM\ManyToOne(inversedBy: 'postDownvotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $votedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->votedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getVotedAt(): ?\DateTimeImmutable
    {
        return $this->votedAt;
    }

    public function setVotedAt(\DateTimeImmutable $votedAt): static
    {
        $this->votedAt = $votedAt;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $postedAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'replies')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    private Collection $replies;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Post $post = null;

    /**
     * @var Collection<int, CommentUpvote>
     */
    #[ORM\OneToMany(targetEntity: CommentUpvote::class, mappedBy: 'comment', orphanRemoval: true)]
    private Collection $upvotes;

    /**
     * @var Collection<int, CommentDownvote>
     */
    #[ORM\OneToMany(targetEntity: CommentDownvote::class, mappedBy: 'comment', orphanRemoval: true)]
    private Collection $downvotes;

    public function __construct()
    {
        $this->replies = new ArrayCollection();
        $this->upvotes = new ArrayCollection();
        $this->downvotes = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->postedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPostedAt(): ?\DateTimeImmutable
    {
        return $this->postedAt;
    }

    public function setPostedAt(\DateTimeImmutable $postedAt): static
    {
        $this->postedAt = $postedAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function addReply(self $reply): static
    {
        if (!$this->replies->contains($reply)) {
            $this->replies->add($reply);
            $reply->setParent($this);
        }

        return $this;
    }

    public function removeReply(self $reply): static
    {
        if ($this->replies->removeElement($reply)) {
            // set the owning side to null (unless already changed)
            if ($reply->getParent() === $this) {
                $reply->setParent(null);
            }
        }

        return $this;
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

    /**
     * @return Collection<int, CommentUpvote>
     */
    public function getUpvotes(): Collection
    {
        return $this->upvotes;
    }

    public function addUpvote(CommentUpvote $upvote): static
    {
        if (!$this->upvotes->contains($upvote)) {
            $this->upvotes->add($upvote);
            $upvote->setComment($this);
        }

        return $this;
    }

    public function removeUpvote(CommentUpvote $upvote): static
    {
        if ($this->upvotes->removeElement($upvote)) {
            // set the owning side to null (unless already changed)
            if ($upvote->getComment() === $this) {
                $upvote->setComment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CommentDownvote>
     */
    public function getDownvotes(): Collection
    {
        return $this->downvotes;
    }

    public function addDownvote(CommentDownvote $downvote): static
    {
        if (!$this->downvotes->contains($downvote)) {
            $this->downvotes->add($downvote);
            $downvote->setComment($this);
        }

        return $this;
    }

    public function removeDownvote(CommentDownvote $downvote): static
    {
        if ($this->downvotes->removeElement($downvote)) {
            // set the owning side to null (unless already changed)
            if ($downvote->getComment() === $this) {
                $downvote->setComment(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post as MetadataPost;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['post:read']],
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['post:read']],
        ),
        new MetadataPost(
            normalizationContext: ['groups' => ['post:read']],
            denormalizationContext: ['groups' => ['post:create']],
            // security: 'is_granted("ROLE_USER")',
        ),
        new Patch(
            normalizationContext: ['groups' => ['post:read']],
            denormalizationContext: ['groups' => ['post:update']],
            security: 'is_granted("ROLE_USER")',
        ),
    ]
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[Groups('post:read')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['post:read', 'post:create', 'post:update'])]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Groups(['post:read', 'post:create', 'post:update'])]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[Groups('post:read')]
    #[ORM\Column]
    private ?\DateTimeImmutable $postedAt = null;

    #[Groups(['post:read', 'post:create'])]
    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post')]
    private Collection $comments;

    #[Groups(['post:read', 'post:create'])]
    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Topic $topic = null;

    /**
     * @var Collection<int, PostUpvote>
     */
    #[ORM\OneToMany(targetEntity: PostUpvote::class, mappedBy: 'post', orphanRemoval: true)]
    private Collection $upvotes;

    /**
     * @var Collection<int, PostDownvote>
     */
    #[ORM\OneToMany(targetEntity: PostDownvote::class, mappedBy: 'post', orphanRemoval: true)]
    private Collection $downvotes;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->upvotes = new ArrayCollection();
        $this->downvotes = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPersist(): void
    {
        $this->postedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
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

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): static
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * @return Collection<int, PostUpvote>
     */
    public function getUpvotes(): Collection
    {
        return $this->upvotes;
    }

    public function addUpvote(PostUpvote $upvote): static
    {
        if (!$this->upvotes->contains($upvote)) {
            $this->upvotes->add($upvote);
            $upvote->setPost($this);
        }

        return $this;
    }

    public function removeUpvote(PostUpvote $upvote): static
    {
        if ($this->upvotes->removeElement($upvote)) {
            // set the owning side to null (unless already changed)
            if ($upvote->getPost() === $this) {
                $upvote->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PostDownvote>
     */
    public function getDownvotes(): Collection
    {
        return $this->downvotes;
    }

    public function addDownvote(PostDownvote $downvote): static
    {
        if (!$this->downvotes->contains($downvote)) {
            $this->downvotes->add($downvote);
            $downvote->setPost($this);
        }

        return $this;
    }

    public function removeDownvote(PostDownvote $downvote): static
    {
        if ($this->downvotes->removeElement($downvote)) {
            // set the owning side to null (unless already changed)
            if ($downvote->getPost() === $this) {
                $downvote->setPost(null);
            }
        }

        return $this;
    }
}

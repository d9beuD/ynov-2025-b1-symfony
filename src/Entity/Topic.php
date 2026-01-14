<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Post as EntityPost;
use App\Repository\TopicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['topic:read']],
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['topic:read']],
        ),
        new Post(
            denormalizationContext: ['groups' => ['topic:write']],
            normalizationContext: ['groups' => ['topic:read']],
            security: 'is_granted("ROLE_USER")',
        ),
        new Patch(
            denormalizationContext: ['groups' => ['topic:write']],
            normalizationContext: ['groups' => ['topic:read']],
            security: 'is_granted("ROLE_USER")',
        ),
    ],
)]
#[ORM\Entity(repositoryClass: TopicRepository::class)]
class Topic
{
    #[Groups('topic:read')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['topic:read', 'topic:write'])]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Groups(['topic:read', 'topic:write'])]
    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, EntityPost>
     */
    #[ORM\OneToMany(targetEntity: EntityPost::class, mappedBy: 'topic', orphanRemoval: true)]
    private Collection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, EntityPost>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(EntityPost $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setTopic($this);
        }

        return $this;
    }

    public function removePost(EntityPost $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getTopic() === $this) {
                $post->setTopic(null);
            }
        }

        return $this;
    }
}

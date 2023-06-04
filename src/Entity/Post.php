<?php

namespace App\Entity;

use App\Repository\PostRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPinned = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Like", mappedBy="post", cascade={"persist", "remove"})
     */
    private $likes;

    /**
     * @ORM\ManyToMany(targetEntity=PostTag::class, inversedBy="posts")
     */
    private $posttag;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

    /**
     * @ORM\OneToOne(targetEntity=Topic::class, mappedBy="post", cascade={"persist", "remove"})
     */
    private $topic;

    public function __toString()
    {
        return $this->title;
    }

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->likes = new ArrayCollection();
        $this->posttag = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isIsPinned(): ?bool
    {
        return $this->isPinned;
    }

    public function setIsPinned(?bool $isPinned): self
    {
        $this->isPinned = $isPinned;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getPost() === $this) {
                $like->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PostTag>
     */
    public function getPosttag(): Collection
    {
        return $this->posttag;
    }

    public function addPosttag(PostTag $posttag): self
    {
        if (!$this->posttag->contains($posttag)) {
            $this->posttag[] = $posttag;
        }

        return $this;
    }

    public function removePosttag(PostTag $posttag): self
    {
        $this->posttag->removeElement($posttag);

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): self
    {
        // unset the owning side of the relation if necessary
        if ($topic === null && $this->topic !== null) {
            $this->topic->setPost(null);
        }

        // set the owning side of the relation if necessary
        if ($topic !== null && $topic->getPost() !== $this) {
            $topic->setPost($this);
        }

        $this->topic = $topic;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Entity\Post;
use App\Entity\Commentaire;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TopicRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=TopicRepository::class)
 */
class Topic
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Post::class, inversedBy="topic", cascade={"persist", "remove"})
     */
    private $post;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="topic")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setTopic($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getTopic() === $this) {
                $commentaire->setTopic(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\AbonnementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AbonnementRepository::class)
 */
class Abonnement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="abonnes")
     */
    private $abonne;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="abonnements")
     */
    private $abonnement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbonne(): ?User
    {
        return $this->abonne;
    }

    public function setAbonne(?User $abonne): self
    {
        $this->abonne = $abonne;

        return $this;
    }

    public function getAbonnement(): ?User
    {
        return $this->abonnement;
    }

    public function setAbonnement(?User $abonnement): self
    {
        $this->abonnement = $abonnement;

        return $this;
    }
}

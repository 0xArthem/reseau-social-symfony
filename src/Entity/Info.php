<?php

namespace App\Entity;

use App\Repository\InfoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InfoRepository::class)
 */
class Info
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
    private $header;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getheader(): ?string
    {
        return $this->header;
    }

    public function setheader(?string $header): self
    {
        $this->header = $header;

        return $this;
    }
}

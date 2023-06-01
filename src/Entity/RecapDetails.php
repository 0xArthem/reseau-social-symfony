<?php

namespace App\Entity;

use App\Repository\RecapDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecapDetailsRepository::class)
 */
class RecapDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="recapDetails")
     */
    private $orderProduct;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $product;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="float")
     */
    private $totalRecap;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderProduct(): ?Order
    {
        return $this->orderProduct;
    }

    public function setOrderProduct(?Order $orderProduct): self
    {
        $this->orderProduct = $orderProduct;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(string $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTotalRecap(): ?float
    {
        return $this->totalRecap;
    }

    public function setTotalRecap(float $totalRecap): self
    {
        $this->totalRecap = $totalRecap;

        return $this;
    }
}

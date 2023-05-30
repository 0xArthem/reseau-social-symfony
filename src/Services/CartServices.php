<?php

namespace App\Services;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartServices {

    private $requestStack;
    private $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;   
        $this->em = $em;
    }

    private function getSession() {
        return $this->requestStack->getSession();
    }

    public function addToCart(int $id): void {
        $card = $this->requestStack->getSession()->get('cart', []);
        if (!empty($card[$id])) {
            $card[$id]++;
        }
        else {
            $card[$id] = 1;
        }
        $this->getSession()->set('cart', $card);
    }

    public function removeCartAll() {
        return $this->getSession()->remove('cart');
    }

    public function getTotal() : array{
        $cart = $this->getSession()->get('cart');
        $cartData = [];
        foreach ($cart as $id => $quantity) {
            $product = $this->em->getRepository(Product::class)->findOneBy(['id' => $id]);
            if (!$product) {
                // supprimer le produit + sortir de la boucle
            }
            $cartData[] = [
               'product' => $product,
               'quantity' => $quantity
            ];
        }
        return $cartData;
    }
}
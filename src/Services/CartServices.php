<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\RequestStack;

class CartServices {

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;   
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
        $this->getSession('cart', $card);
    }
}
<?php

namespace App\Controller\Cart;

use App\Services\CartServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $cartServices;

    public function __construct(CartServices $cartServices)
    {
        $this->cartServices = $cartServices;
    }

    /**
     * @Route("/cart", name="app_cart")
     */
    public function index(): Response
    {
        $cart = $this->cartServices->getFullCart();

        if (!isset($cart['products'])) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function addToCart($id): Response
    {
        $this->cartServices->addToCart($id);

        return $this->redirectToRoute('app_cart');
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete")
     */
    public function deleteFromCart($id): Response
    {
        $this->cartServices->deleteFromCart($id);

        return $this->redirectToRoute('app_cart');
    }
    /**
     * 
     * @Route("/cart/deleteAll/{id}", name="cart_deleteAll")
     */
    public function deleteAllToCart($id): Response
    {
        $this->cartServices->deleteAllToCart($id);

        return $this->redirectToRoute('app_cart');
    }
}

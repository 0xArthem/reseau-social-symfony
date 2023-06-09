<?php

namespace App\Controller;

use App\Services\CartServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    /**
     * @Route("/panier", name="cart_index")
     */
    public function index(CartServices $cartServices): Response
    {
    // dd($cartServices->getTotal());
       return $this->render('product/index.html.twig', [
            'cart' => $cartServices->getTotal(),
        ]);
    }

    /**
     * @Route("/mon-panier/ajouter/{id}", name="addToCart")
     */
    public function addToCart(CartServices $cartServices, $id): Response
    {
       $cartServices->addToCart($id);

        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/mon-panier/supprimer/{id}", name="decrementToCart")
     */
    public function decrementToCart(CartServices $cartServices, $id): Response
    {
       $cartServices->decrementToCart($id);

        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/mon-panier/supprimer-le-panier", name="removeCartAll")
     */
    public function removeCartAll(CartServices $cartServices): Response
    {
       $cartServices->removeCartAll();

        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/mon-panier/supprimer-le-produit/{id}", name="removeToCart")
     */
    public function removeToCart(CartServices $cartServices, $id): Response
    {
       $cartServices->removeToCart($id);

        return $this->redirectToRoute('cart_index');
    }
}

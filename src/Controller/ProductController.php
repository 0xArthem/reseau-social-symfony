<?php

namespace App\Controller;

use App\Services\CartServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    /**
     * @Route("/mon-panier", name="cart_index")
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
    //    dd($cartServices);

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
}

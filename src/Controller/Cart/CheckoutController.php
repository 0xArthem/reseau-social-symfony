<?php

namespace App\Controller\Cart;

use App\Form\CheckoutType;
use App\Services\CartServices;
use App\Services\OrderServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutController extends AbstractController
{
    private $cartServices;
    private $session;
    private $manager;

    public function __construct(EntityManagerInterface $manager, CartServices $cartServices, SessionInterface $session)
    {
        $this->cartServices = $cartServices;
        $this->session = $session;
        $this->manager = $manager;
    }

    /**
     * @Route("/checkout", name="app_checkout")
     */
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        $cart = $this->cartServices->getFullCart();

        if (!$cart) {
            return $this->redirectToRoute('app_home');
        }
        if (!$user->getAddresses()->getValues()) {
            $this->addFlash('checkout_message', 'Veuillez ajouter une adresse à votre compte pour pouvoir continuer.');
            return $this->redirectToRoute('app_address_new');
        }

        $form = $this->createForm(CheckoutType::class, null, ['user' => $user]);
        $form->handleRequest($request);

        // traitement du formulaire dans checkout_confirm

        return $this->render('checkout/index.html.twig', [
            'cart' => $cart,
            'checkout' => $form->createView(),
        ]);
    }
    /**
     * @Route("/checkout/confirm", name="checkout_confirm")
     */
    public function confirm(Request $request, OrderServices $orderServices, SessionInterface $session): Response
    {
        $user = $this->getUser();

        $cart = $this->cartServices->getFullCart();

        if (!isset($cart['products'])) {
            return $this->redirectToRoute('app_home');
        }

        if (!$user->getAddresses()->getValues()) {
            $this->addFlash('checkout_message', 'Veuillez ajouter une adresse à votre compte pour pouvoir continuer.');
            return $this->redirectToRoute('app_address_new');
        }

        $form = $this->createForm(CheckoutType::class, null, ['user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() || $this->session->get('checkout_data')) {

            if ($this->session->get('checkout_data')) {
                $data = $this->session->get('checkout_data');
            } else {
                $data = $form->getData();
                $this->session->set('checkout_data', $data);
            }

            $address = $data['address'];
            $carrier = $data['carrier'];
            $information = $data['information'];

            // sauvegarder le panier
            $cart['checkout'] = $data;
            $reference = $orderServices->saveCart($cart, $user, $session);

            // dd($reference);
            // dd($cart);


            return $this->render('checkout/confirm.html.twig', [
                'address' => $address,
                'carrier' => $carrier,
                'information' => $information,
                'cart' => $cart,
                'reference' => $reference,
                'checkout' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('app_home');
    }
}

<?php

namespace App\Controller\Stripe;

use Stripe\Stripe;
use App\Entity\Cart;
use Stripe\Checkout\Session;
use App\Services\CartServices;
use App\Services\OrderServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeCheckoutSessionController extends AbstractController
{
    /**
     * @Route("/create-checkout-session/{reference}", name="create-checkout-session")
     */
    public function index(?Cart $cart, OrderServices $orderServices, EntityManagerInterface $manager): JsonResponse
    {

        $order = $orderServices->createOrder($cart);

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $orderServices->getLineItems($cart),
            'mode' => 'payment',
            'success_url' => $_ENV['YOUR_DOMAINE'] . 'stripe-payment-success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $_ENV['YOUR_DOMAINE'] . 'stripe-payment-cancel/{CHECKOUT_SESSION_ID}',
            // 'automatic_tax' => [
            //     'enabled' => true,
            // ],
        ]);

        $order->setStripeCheckoutSessionId($checkout_session->id);
        $manager->flush();

        return $this->json(['id' => $checkout_session->id]);
    }
}

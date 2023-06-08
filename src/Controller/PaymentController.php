<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/panier/create-session-stripe/{reference}", name="payment_stripe")
     */
    public function stripeCheckout($reference): Response
    {

        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);
        dd($order);

        Stripe::setApiKey('sk_test_51M3KrLJysBhvIGnlEYRSx3rY9tXPoyJy2DZ8meJWaByaMO2A13ne45OjjDNmjfB1j8YDgAWCUe5mUzKAfxkLg5Jc001tEfq9X8');

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [[
              # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
              'price' => '{{PRICE_ID}}',
              'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
}

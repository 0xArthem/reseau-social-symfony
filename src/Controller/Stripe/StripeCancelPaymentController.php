<?php

namespace App\Controller\Stripe;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeCancelPaymentController extends AbstractController
{
    /**
     * @Route("/stripe-payment-cancel/{stripeCheckoutSessionId}", name="stripe-payment-cancel")
     */
    public function index(?Order $order): Response
    {
        return $this->render('stripe/stripe_cancel_payment/index.html.twig', [
            'controller_name' => 'StripeCancelPaymentController',
        ]);
    }
}

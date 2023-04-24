<?php

namespace App\Controller\Stripe;

use App\Entity\Cart;
use App\Entity\Order;
use App\Services\CartServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeSuccessPaymentController extends AbstractController
{
    /**
     * @Route("/stripe-payment-success/{stripeCheckoutSessionId}", name="stripe-payment-success")
     */
    public function index(?Order $order, ?Cart $cart,  CartServices $cartServices, EntityManagerInterface $manager): Response
    {
        if (!$order) {
            return $this->redirectToRoute('app_home');
        }

        if (!$order->isIsPaid()) {
            $order->setIsPaid(true);
            $order->setIsIsProcess(true);
            $manager->flush();
            $cartServices->deleteCart();
        }

        return $this->render('stripe/stripe_success_payment/index.html.twig', [
            'order' => $order,
        ]);
    }
}

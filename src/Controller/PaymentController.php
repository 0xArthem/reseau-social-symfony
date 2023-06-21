<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Services\CartServices;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{

    private $em;
    private $generator;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $url)
    {
        $this->em = $em;
        $this->generator = $url;
    }

    /**
     * @Route("/panier/create-session-stripe/{reference}", name="payment_stripe")
     */
    public function stripeCheckout($reference): Response
    {

        $productStripe = [];

        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);
        if (!$order) {
            return $this->redirectToRoute('cart_index');
        }

        foreach ($order->getRecapDetails()->getValues() as $product) {
            $productData = $this->em->getRepository(Product::class)->findOneBy(['title' => $product->getProduct()]);
            // dd($productData);
            $productStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $productData->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct()
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $productStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getTransporterPrice()*100,
                'product_data' => [
                    'name' => $order->getTransporterName(),
                ],
            ],
            'quantity' => 1,
        ];

        // dd($productStripe);

        Stripe::setApiKey('sk_test_51M3KrLJysBhvIGnlEYRSx3rY9tXPoyJy2DZ8meJWaByaMO2A13ne45OjjDNmjfB1j8YDgAWCUe5mUzKAfxkLg5Jc001tEfq9X8');
        // dd($checkout_session);
        $checkout_session = \Stripe\Checkout\Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => $productStripe,
            'mode' => 'payment',
            'success_url' => $this->generator->generate('payment_success', [
                'reference' => $order->getReference()
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generator->generate('payment_error', [
                'reference' => $order->getReference()
            ], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $this->em->flush();

        return new RedirectResponse($checkout_session->url);
    }

    /**
     * @Route("/panier/success/{reference}", name="payment_success")
     */
    public function StripeSuccess($reference, CartServices $cartServices): Response {
        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);
        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('cart_index');
        }
        if (!$order->isIsPaid()) {
            $cartServices->removeCartAll();
            $order->setIsPaid(1);
            $this->em->flush();
        }
        return $this->render('order/success.html.twig', [
            'order' => $order
        ]);
    }

    /**
     * @Route("/panier/error/{reference}", name="payment_error")
     */
    public function StripeError($reference): Response {
        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);
        return $this->render('order/error.html.twig', [
            'order' => $order
        ]);
    }
}

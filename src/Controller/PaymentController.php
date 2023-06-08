<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
                    'quantity' => $product->getQuantity(),
                ]
            ];
        }

        $productStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getTransporterPrice(),
                'product_data' => [
                    'name' => $product->getProduct()
                ],
                'quantity' => $order->getTransporterName(),
            ]
        ];

        dd($productStripe);

        Stripe::setApiKey('sk_test_51M3KrLJysBhvIGnlEYRSx3rY9tXPoyJy2DZ8meJWaByaMO2A13ne45OjjDNmjfB1j8YDgAWCUe5mUzKAfxkLg5Jc001tEfq9X8');

        $checkout_session = \Stripe\Checkout\Session::create([
            // 'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
              $productStripe
            ]],
            'mode' => 'payment',
            // 'success_url' => $YOUR_DOMAIN . '/success.html',
            // 'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        return new RedirectResponse($checkout_session->url);
    }
}

<?php

namespace App\Services;

use DateTime;
use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\CartDetails;
use App\Entity\OrderDetails;
use App\Services\CartServices;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderServices
{
    private $manager;
    private $repoProduct;
    public function __construct(EntityManagerInterface $manager, ProductRepository $repoProduct)
    {
        $this->manager = $manager;
        $this->repoProduct = $repoProduct;
    }

    public function createOrder($cart)
    {
        $order = new Order();
        $order->setReference($cart->getReference())
            ->setCarrierName($cart->getCarrierName())
            ->setCarrierPrice($cart->getCarrierPrice() / 100)
            ->setFullName($cart->getFullName())
            ->setDeliveryAddress($cart->getDeliveryAddress())
            ->setMoreInformations($cart->getMoreInformations())
            ->setQuantity($cart->getQuantity())
            ->setSubtotalHT($cart->getSubtotalHT() / 100)
            ->setTaxe($cart->getTaxe() / 100)
            ->setSubTotalTTC($cart->getSubtotalTTC() / 100)
            ->setUser($cart->getUser())
            ->setCreatedAt($cart->getCreatedAt());
        $this->manager->persist($order);

        $products = $cart->getCartDetails()->getValues();

        foreach ($products as $cart_product) {
            $orderDetails =  new OrderDetails();

            $productPrice = $cart_product->getProductPrice() / 100;
            $priceInCents = intval($productPrice * 100);

            $orderDetails->setOrders($order)
                ->setProductName($cart_product->getProductName())
                ->setProductPrice($productPrice)
                ->setQuantity($cart_product->getQuantity())
                ->setSubTotalHT($cart_product->getSubtotalHT() / 100)
                ->setSubTotalTTC($cart_product->getSubtotalTTC() / 100)
                ->setTaxe($cart_product->getTaxe() / 100);
            $this->manager->persist($orderDetails);
        }

        $this->manager->flush();

        return $order;
    }

    public function getLineItems($cart)
    {
        $cartDetails = $cart->getCartDetails();

        $line_items = [];
        foreach ($cartDetails as $details) {
            $product = $this->repoProduct->findOneByName($details->getProductName());

            $priceInCents = number_format($product->getPrice(), 2, '.', '') * 100;

            $line_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $priceInCents,
                    'product_data' => [
                        'name' => $product->getName(),
                    ],
                ],
                'quantity' => $details->getQuantity(),
            ];
        }

        // taxe
        $line_items[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $cart->getTaxe(),
                'product_data' => [
                    'name' => 'TVA (20%)',
                ],
            ],
            'quantity' => 1,
        ];

        // transporteur
        $line_items[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $cart->getCarrierPrice(),
                'product_data' => [
                    'name' => $cart->getCarrierName(),
                ],
            ],
            'quantity' => 1,
        ];

        return $line_items;
    }

    public function saveCart($data, $user, SessionInterface $session)
    {
        $session->clear();

        $cart = new Cart();

        $reference = $this->generateUuid();
        $address = $data['checkout']['address'];
        $carrier = $data['checkout']['carrier'];
        $information = $data['checkout']['information'];

        $cart->setReference($reference)
            ->setCarrierName($carrier->getName())
            ->setCarrierPrice($carrier->getPrice() / 100)
            ->setFullName($address->getFullName())
            ->setDeliveryAddress($address)
            ->setMoreInformations($information)
            ->setQuantity($data['data']['quantity_cart'])
            ->setSubTotalHT($data['data']['subTotalHT'])
            ->setTaxe($data['data']['taxe'])
            ->setSubTotalTTC(round($data['data']['subTotalTTC'] + $carrier->getPrice() / 100, 2))
            ->setUser($user)
            ->setCreatedAt(new DateTime());
        $this->manager->persist($cart);

        $cart_details_array = [];

        foreach ($data['products'] as $products) {
            $cartDetails = new CartDetails();

            $subTotal = $products['quantity'] * $products['product']->getPrice();

            $cartDetails->setCarts($cart)
                ->setProductName($products['product']->getName())
                ->setProductPrice($products['product']->getPrice())
                ->setQuantity($products['quantity'])
                ->setSubTotalHT($subTotal)
                ->setSubTotalTTC($subTotal * 1.2)
                ->setTaxe($subTotal * 0.2);

            $this->manager->persist($cartDetails);
            $cart_details_array[] = $cartDetails;
        }

        $this->manager->flush();

        return $reference;
    }

    public function generateUuid()
    {
        mt_srand((float)microtime() * 100000);

        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);

        $uuid = ""
            . substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12) . $hyphen;
        return $uuid;
    }
}

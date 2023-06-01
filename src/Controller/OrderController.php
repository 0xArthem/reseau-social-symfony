<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\RecapDetails;
use App\Form\OrderType;
use App\Services\CartServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/order/create", name="order_index")
     */
    public function index(CartServices $cartServices): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cartServices->getTotal()
        ]);
    }

    /**
     * @Route("/order/prepare", name="order_prepare", methods={"POST"})
     */
    public function prepareOrder(Request $request, CartServices $cartServices): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datetime = new \DateTime('now');
            $transporter = $form->get('transporter')->getData();

            $delivery = $form->get('addresses')->getData();
            $deliveryForOrder = $delivery->getFirstName().' '.$delivery->getLastName();
            $deliveryForOrder .= ' ' . $delivery->getPhone();
            if ($delivery->getCompany()) {
                $deliveryForOrder .= ' - ' . $delivery->getCompany();
            }
            $deliveryForOrder .= ' ' . $delivery->getAddress();
            $deliveryForOrder .= ' ' . $delivery->getPostalCode() . ' ' . $delivery->getCity();
            $deliveryForOrder .= ' ' . $delivery->getCountry();
            
            $order = new Order();
            $reference = $datetime->format('dmY').'-'.uniqid();
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCreatedAt($datetime);
            $order->setDelivery($deliveryForOrder);
            $order->setTransporterName($transporter->getTitle());
            $order->setTransporterPrice($transporter->getPrice());
            $order->setIsPaid(0);

            $order->setMethod('stripe');

            $this->em->persist($order);

            foreach ($cartServices->getTotal() as $product) {
                $recapDetails = new RecapDetails();
                $recapDetails->setOrderProduct($order);
                $recapDetails->setQuantity($product['quantity']);
                $recapDetails->setPrice($product['product']->getPrice());
                $recapDetails->setProduct($product['product']->getTitle());
                $recapDetails->setTotalRecap($product['product']->getPrice() * $product['quantity']);
                
                $this->em->persist($recapDetails);
            }

            $this->em->flush();
            return $this->render('order/recap.html.twig', [
                'method' => $order->getMethod(),
                'cart' => $cartServices->getTotal(),
                'transporter' => $transporter,
                'delivery' => $deliveryForOrder,
                'reference' => $order->getReference()
            ]);
        }

        return $this->redirectToRoute('app_index');
    }
}

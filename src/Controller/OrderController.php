<?php

namespace App\Controller;

use App\Entity\Order;
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
    public function prepareOrder(Request $request): Response {
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
            $deliveryForOrder .= '</br>' . $delivery->getPhone();
            if ($delivery->getCompany()) {
                $deliveryForOrder .= ' - ' . $delivery->getCompany();
            }
            $deliveryForOrder .= '</br>' . $delivery->getAddress();
            $deliveryForOrder .= '</br>' . $delivery->getPostalCode() . ' ' . $delivery->getCity();
            $deliveryForOrder .= '</br>' . $delivery->getCountry();
            // dd($deliveryForOrder);
            
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
            $this->em->flush();
        }

        return $this->render('order/recap.html.twig', [
        ]);
    }
}

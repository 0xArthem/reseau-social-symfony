<?php

namespace App\Controller\Account;

use App\Repository\OrderDetailsRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="app_account")
     */
    public function index(OrderRepository $repoOrder, OrderDetailsRepository $repoOrderDetails): Response
    {
        $orders = $repoOrder->findBy(['isPaid' => true, 'user' => $this->getUser()], ['id' => 'DESC'], null, null, ['orderDetails']);

        return $this->render('account/index.html.twig', [
            'orders' => $orders
        ]);
    }
}

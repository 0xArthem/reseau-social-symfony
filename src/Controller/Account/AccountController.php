<?php

namespace App\Controller\Account;

use App\Form\EditProfilType;
use App\Repository\OrderRepository;
use App\Repository\OrderDetailsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    /**
     * @Route("/modifier-profil", name="app_account_edit_profil")
     */
    public function editProfile(Request $request): Response
    {
        $user = $this->getUser(); // récupère l'utilisateur connecté
        $form = $this->createForm(EditProfilType::class, $user); // crée le formulaire et le lie à l'utilisateur

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

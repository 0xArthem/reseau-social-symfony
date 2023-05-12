<?php

namespace App\Controller\Account;

use App\Entity\User;
use App\Services\AbonnementServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/compte")
 */
class AbonnementController extends AbstractController
{   
   /**
     * @Route("/{username}/abonnements", name="app_account_abonnements")
     */
    public function showAbonnement(Request $request, AbonnementServices $abonnementServices): Response
    {
        return $abonnementServices->showAbonnement($request);
    }

    /**
     * @Route("/{username}/abonnement", name="app_account_subscribe")
     */
    public function abonnement(User $userToFollow, AbonnementServices $abonnementServices, SessionInterface $session): Response
    {
        $user = $this->getUser();
        $abonnementServices->setSession($session); // Injecte la session dans le service
        return $abonnementServices->abonnement($user, $userToFollow);
    }

    /**
     * @Route("/{username}/desabonnement", name="app_account_unsubscribe")
     */
    public function desabonnement(User $userToFollow, AbonnementServices $abonnementServices): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            // Si l'utilisateur n'est pas connecté, redirection vers la page de connexion
            return $this->redirectToRoute('app_login');
        }
        return $abonnementServices->desabonnement($user, $userToFollow);
    }
}

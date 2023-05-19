<?php

namespace App\Controller\Account;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Services\AccountServices;

/**
 * @Route("/compte")
 */
class AccountController extends AbstractController
{
    private $accountServices;

    public function __construct(AccountServices $accountServices)
    {
        $this->accountServices = $accountServices;
    }

    /**
     * @Route("/{username}", name="app_account")
     */
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        
        return $this->accountServices->index($request, $user);

    }

    /**
     * @Route("/{username}/parametres", name="app_account_type")
     */
    public function accountType(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // récupère l'utilisateur connecté
        $user = $this->getUser();

        return $this->accountServices->accountType($request, $user, $passwordEncoder);
    }
}
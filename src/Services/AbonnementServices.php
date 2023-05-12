<?php

namespace App\Services;

use App\Entity\User;
use Twig\Environment;
use App\Entity\Abonnement;
use App\Repository\UserRepository;
use App\Repository\AbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class AbonnementServices
{
    private $security;
    private $twig;
    private $userRepository;
    private $entityManager;
    private $abonnementRepository;
    private $session;
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator, SessionInterface $session, EntityManagerInterface $entityManager, AbonnementRepository $abonnementRepository, Security $security, Environment $twig, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->abonnementRepository = $abonnementRepository;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
    }

    public function showAbonnement(Request $request): Response
    {
        // récupère l'utilisateur connecté
        $user = $this->security->getUser();
        // récupère l'utilisateur du compte visité à partir de son nom d'utilisateur dans l'URL
        $visitedUser = $this->userRepository->findOneBy(['username' => $request->attributes->get('username')]);

        if ($visitedUser === $user) {
            $abonnements = $user->getAbonnements();
            $abonnes = $user->getAbonnes();
        }

        return new Response($this->twig->render('account/abonnements.html.twig', [
            'abonnements' => $abonnements,
            'abonnes' => $abonnes
        ]));
    }

    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }

    public function abonnement(User $user, User $userToFollow): Response
    {
        if (!$user) {
            // Si l'utilisateur n'est pas connecté, redirection vers la page de connexion
            $url = $this->urlGenerator->generate('app_login');
            return new RedirectResponse($url);
        }

        $abonnement = $this->abonnementRepository->findOneBy(['abonne' => $user, 'abonnement' => $userToFollow]);

        if ($abonnement) {
            // Si l'utilisateur est déjà abonné, message d'erreur
            $this->session->getFlashBag()->add('error', 'Vous êtes déjà abonné à cet utilisateur.');
            $url = $this->urlGenerator->generate('app_account', ['username' => $userToFollow->getUsername()]);
            return new RedirectResponse($url);
        }

        if (!$userToFollow) {
            // Si l'utilisateur à suivre n'existe pas, message d'erreur
            $this->session->getFlashBag()->add('error', 'Cet utilisateur n\'existe pas.');
            $url = $this->urlGenerator->generate('app_account', ['username' => $userToFollow->getUsername()]);
            return new RedirectResponse($url);
        }

        $newAbonnement = new Abonnement();
        $newAbonnement->setAbonne($user);
        $newAbonnement->setAbonnement($userToFollow);
        $this->entityManager->persist($newAbonnement);
        $this->entityManager->flush();

        $url = $this->urlGenerator->generate('app_account', ['username' => $userToFollow->getUsername()]);
        return new RedirectResponse($url);
    }

    public function desabonnement(User $user, User $userToFollow): Response
    {
        $abonnement = $this->abonnementRepository->findOneBy(['abonne' => $user, 'abonnement' => $userToFollow]);

        if (!$abonnement) {
            // Si l'utilisateur n'est pas abonné, message d'erreur
            $this->session->getFlashBag()->add('error', 'Vous n\'êtes pas abonné à cet utilisateur.');
            $url = $this->urlGenerator->generate('app_account', ['username' => $userToFollow->getUsername()]);
            return new RedirectResponse($url);
        }

        $this->entityManager->remove($abonnement);
        $this->entityManager->flush();

        $url = $this->urlGenerator->generate('app_account', ['username' => $userToFollow->getUsername()]);
        return new RedirectResponse($url);
    }
}

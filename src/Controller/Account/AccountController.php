<?php

namespace App\Controller\Account;

use App\Entity\User;
use App\Form\AccountType;
use App\Form\EditProfilType;
use App\Repository\PostRepository;
use App\Services\AbonnementServices;
use App\Repository\AbonnementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/compte")
 */
class AccountController extends AbstractController
{
    private $postRepository;
    private $abonnementRepository;

    public function __construct(PostRepository $postRepository, AbonnementRepository $abonnementRepository)
    {
        $this->postRepository = $postRepository;
        $this->abonnementRepository = $abonnementRepository;
    }

    /**
     * @Route("/{username}", name="app_account")
     */
    public function index(Request $request): Response
    {
        // récupère l'utilisateur connecté
        $user = $this->getUser();
        // récupère l'utilisateur du compte visité à partir de son nom d'utilisateur dans l'URL
        $visitedUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $request->attributes->get('username')]);

        $abonnements = $visitedUser->getAbonnements();
        $abonnes = $visitedUser->getAbonnes();

        $posts = $this->postRepository->findBy(['user' => $visitedUser], ['id' => 'DESC']);
        $postsIsPinned = $this->postRepository->findBy(['isPinned' => true, 'user' => $visitedUser], ['id' => 'DESC']);

        // Vérifie si l'utilisateur visité est l'utilisateur connecté
        if ($visitedUser === $user) {
            return $this->indexLoggedInUser($user, $visitedUser, $abonnements, $abonnes, $posts, $postsIsPinned, $this->abonnementRepository, $request);
        } else {
            return $this->indexOtherUser($user, $visitedUser, $abonnements, $abonnes, $posts, $postsIsPinned, $this->abonnementRepository);
        }
    }

    private function indexLoggedInUser(User $user, User $visitedUser, $abonnements, $abonnes, $posts, $postsIsPinned, AbonnementRepository $abonnementRepository, Request $request): Response
    {
        $form = $this->createForm(EditProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // gérer l'upload de l'image de profil
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('profile_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // gérer les erreurs d'upload ici
                }

                $user->setImage($newFilename);
            }

            $this->getDoctrine()->getManager()->flush();

            // Message de succès
            $this->addFlash('success', 'Votre profil a été correctement mis à jour !');

            return $this->redirectToRoute('app_account', ['username' => $user->getUsername()]);
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            // Message d'erreur
            $this->addFlash('error', 'Votre profil n\'a pas pu être correctement mis à jour.');
            // return $this->redirectToRoute('app_account');
        }

        return $this->render('account/index.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'postsIsPinned' => $postsIsPinned,
            'visitedUser' => $visitedUser,
            'form' => $form->createView(),
            'abonnements' => $abonnements,
            'abonnes' => $abonnes
        ]);
    }

    private function indexOtherUser(User $user, User $visitedUser, $abonnements, $abonnes, $posts, $postsIsPinned, AbonnementRepository $abonnementRepository): Response {
        // On vérifie si l'utilisateur visité est abonné à l'utilisateur connecté (qui regarde donc son profil)
        $isFollowed = $abonnementRepository->findOneBy(['abonne' => $visitedUser, 'abonnement' => $user]) !== null;

        // On vérifie si l'utilisateur connecté est abonné à l'utilisateur visité
        $isSubscribed = $abonnementRepository->findOneBy(['abonne' => $user, 'abonnement' => $visitedUser]) !== null;
        if ($user) {
            $abonnement = $abonnementRepository->findOneBy(['abonne' => $user, 'abonnement' => $visitedUser]);
            $isSubscribed = ($abonnement !== null);
        }

        return $this->render('account/other.html.twig', [
            'visitedUser' => $visitedUser,
            'posts' => $posts,
            'postsIsPinned' => $postsIsPinned,
            'abonnements' => $abonnements,
            'abonnes' => $abonnes,
            'isSubscribed' => $isSubscribed,
            'isFollowed' => $isFollowed
        ]);
    }

        /**
     * @Route("/{username}/parametres", name="app_account_type")
     */
    public function accountType(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // récupère l'utilisateur connecté
        $user = $this->getUser();
        
        // récupère l'utilisateur du compte visité à partir de son nom d'utilisateur dans l'URL
        $visitedUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $request->attributes->get('username')]);

        if ($visitedUser === $user) {
            $form = $this->createForm(AccountType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Hacher le nouveau mot de passe
                $newPassword = $form->get('password')->getData();
                if ($newPassword) {
                    $hashedPassword = $passwordEncoder->encodePassword($user, $newPassword);
                    $user->setPassword($hashedPassword);
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                // Message de succès
                $this->addFlash('success', 'Vos informations ont été correctement mises à jour !');
            }
            if ($form->isSubmitted() && !$form->isValid()) {
                // Message d'erreur
                $this->addFlash('error', 'VVos informations n\'ont pas pu être correctement mises à jour');
            }
        }

        return $this->render('account/accountType.html.twig', [
            'user' => $user,
            'visitedUser' => $visitedUser,
            'form' => $form->createView()
        ]);
    }

    /** abonnements **/

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
<?php

namespace App\Controller\Account;

use App\Entity\User;
use App\Entity\Abonnement;
use App\Form\EditProfilType;
use App\Repository\AbonnementRepository;
use App\Repository\PostRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderDetailsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/compte")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/{username}", name="app_account")
     */
    public function index(PostRepository $postRepository, AbonnementRepository $abonnementRepository, OrderRepository $repoOrder, OrderDetailsRepository $repoOrderDetails, Request $request): Response
    {
        // récupère l'utilisateur connecté
        $user = $this->getUser();
        // récupère l'utilisateur du compte visité à partir de son nom d'utilisateur dans l'URL
        $visitedUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $request->attributes->get('username')]);

        $posts = $postRepository->findBy(['user' => $visitedUser], ['id' => 'DESC']);
        $postsIsPinned = $postRepository->findBy(
            array('isPinned' => true, 'user' => $visitedUser),
            array('id' => 'DESC')
        );

        $orders = $repoOrder->findBy(['isPaid' => true, 'user' => $user], ['id' => 'DESC'], null, null, ['orderDetails']);

        // Vérifie si l'utilisateur visité est l'utilisateur connecté
        if ($visitedUser === $user) {
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

            $abonnements = $user->getAbonnements();
            $abonnes = $user->getAbonnes();

            return $this->render('account/index.html.twig', [
                'user' => $user,
                'posts' => $posts,
                'postsIsPinned' => $postsIsPinned,
                'visitedUser' => $visitedUser,
                'form' => $form->createView(),
                'orders' => $orders,
                'abonnements' => $abonnements,
                'abonnes' => $abonnes
            ]);
        } else {

            $abonnements = $visitedUser->getAbonnements();
            $abonnes = $visitedUser->getAbonnes();

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
            ]);
        }
    }

   /**
     * @Route("/{username}/abonnement", name="app_account_subscribe")
     */
    public function abonnement(Request $request, User $userToFollow, AbonnementRepository $abonnementRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // Si l'utilisateur n'est pas connecté, redirection vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // On vérifie si l'utilisateur connecté n'est pas déjà abonné à cet utilisateur
        $abonnement = $abonnementRepository->findOneBy(['abonne' => $user, 'abonnement' => $userToFollow]);

        if ($abonnement) {
            // Si l'utilisateur est déjà abonné, message d'erreur
            $this->addFlash('error', 'Vous êtes déjà abonné à cet utilisateur.');
            return $this->redirectToRoute('app_account', ['username' => $userToFollow->getUsername()]);
        }

        if (!$userToFollow) {
            // Si l'utilisateur à suivre n'existe pas, message d'erreur
            $this->addFlash('error', 'Cet utilisateur n\'existe pas.');
            return $this->redirectToRoute('app_account', ['username' => $userToFollow->getUsername()]);
        }

        // Si l'utilisateur n'est pas déjà abonné et que l'utilisateur à suivre existe, on crée un nouvel abonnement
        $newAbonnement = new Abonnement();
        $newAbonnement->setAbonne($user);
        $newAbonnement->setAbonnement($userToFollow);
        $em = $this->getDoctrine()->getManager();
        $em->persist($newAbonnement);
        $em->flush();

        return $this->redirectToRoute('app_account', ['username' => $userToFollow->getUsername()]);
    }
}
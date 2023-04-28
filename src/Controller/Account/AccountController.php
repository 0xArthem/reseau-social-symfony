<?php

namespace App\Controller\Account;

use App\Entity\User;
use App\Form\EditProfilType;
use App\Repository\OrderRepository;
use App\Repository\OrderDetailsRepository;
use App\Repository\PostRepository;
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
    public function index(PostRepository $postRepository, OrderRepository $repoOrder, OrderDetailsRepository $repoOrderDetails, Request $request): Response
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

            return $this->render('account/index.html.twig', [
                'user' => $user,
                'posts' => $posts,
                'postsIsPinned' => $postsIsPinned,
                'visitedUser' => $visitedUser,
                'form' => $form->createView(),
                'orders' => $orders,
            ]);
        } else {
            return $this->render('account/other.html.twig', [
                'visitedUser' => $visitedUser,
                'posts' => $posts,
                'postsIsPinned' => $postsIsPinned
            ]);
        }
    }
}
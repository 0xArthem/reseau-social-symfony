<?php

namespace App\Controller\Account;

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
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="app_account")
     */
    public function index(PostRepository $postRepository, OrderRepository $repoOrder, OrderDetailsRepository $repoOrderDetails, Request $request): Response
    {
        $orders = $repoOrder->findBy(['isPaid' => true, 'user' => $this->getUser()], ['id' => 'DESC'], null, null, ['orderDetails']);

        // récupère l'utilisateur connecté
        $user = $this->getUser();
        // crée le formulaire et le lie à l'utilisateur
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
            
            return $this->redirectToRoute('app_account');
        }
        if ($form->isSubmitted() && !$form->isValid()) {
            // Message d'erreur
            $this->addFlash('error', 'Votre profil n\'a pas pu être correctement mis à jour.');
            // return $this->redirectToRoute('app_account');
        }
        $posts = $postRepository->findBy(array(), array('id' => 'DESC'));

        return $this->render('account/index.html.twig', [
            'orders' => $orders,
            'form' => $form->createView(),
            'posts' => $posts
        ]);
    }
}

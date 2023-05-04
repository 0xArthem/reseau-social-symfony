<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/compte/{username}/post")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/new", name="app_post_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PostRepository $postRepository, $username): Response
    {
        // récupère l'utilisateur connecté
        $userConnected = $this->getUser();

        // récupérer l'utilisateur correspondant à l'username
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);

        // Vérifier si l'utilisateur connecté est l'auteur du post
        if ($user !== $userConnected) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // gérer l'upload de l'image du post
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('post_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // gérer les erreurs d'upload ici
                }

                $post->setImage($newFilename);
            }

            $post->setUser($user);
            $postRepository->add($post, true);

            return $this->redirectToRoute('app_account', ['username' => $user->getUsername()]);
        }

        return $this->renderForm('post/new.html.twig', [
            'user' => $user,
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_post_show", methods={"GET"})
     */
    public function show(Post $post, $username): Response
    {
        // récupérer l'utilisateur correspondant à l'username
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);
        
        $likes = $post->getLikes();
        $countLikes = $likes->count();


        return $this->render('post/show.html.twig', [
            'user' => $user,
            'post' => $post,
            'countLikes' => $countLikes
        ]);
    }


    /**
     * @Route("/{id}/like", name="app_post_like", methods={"GET"})
     */
    public function like(Post $post, $username): Response
    {
        return $this->redirectToRoute('app_post_show', ['id' => $post->getId(), 'username' => $username]);
    }

    /**
     * @Route("/{id}/edit", name="app_post_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Post $post, PostRepository $postRepository, $username): Response
    {

        // récupère l'utilisateur connecté
        $userConnected = $this->getUser();

        // récupérer l'utilisateur correspondant à l'username
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);

        // Vérifier si l'utilisateur connecté est l'auteur du post
        if ($user !== $userConnected) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // gérer l'upload de l'image du post
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('post_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // gérer les erreurs d'upload ici
                }

                $post->setImage($newFilename);
            }

            $post->setUser($user);
            $postRepository->add($post, true);

            return $this->redirectToRoute('app_account', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'user' => $user,
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_post_delete", methods={"POST"})
     */
    public function delete(Request $request, Post $post, PostRepository $postRepository, $username): Response
    {
        // récupère l'utilisateur connecté
        $userConnected = $this->getUser();

        // récupérer l'utilisateur correspondant à l'username
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);

        // Vérifier si l'utilisateur connecté est l'auteur du post
        if ($user !== $userConnected) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à effectuer cette action');
        }

        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $postRepository->remove($post, true);
        }

        return $this->redirectToRoute('app_account', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);
    }
}

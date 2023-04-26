<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/account/{username}/post")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/new", name="app_post_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PostRepository $postRepository, $username): Response
    {
        // récupérer l'utilisateur correspondant à l'username
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);

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

        return $this->render('post/show.html.twig', [
            'user' => $user,
            'post' => $post,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_post_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Post $post, PostRepository $postRepository, $username): Response
    {
        // récupérer l'utilisateur correspondant à l'username
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);

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
        // récupérer l'utilisateur correspondant à l'username
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);

        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $postRepository->remove($post, true);
        }

        return $this->redirectToRoute('app_account', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);
    }
}

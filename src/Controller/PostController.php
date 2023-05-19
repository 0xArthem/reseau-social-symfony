<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use App\Services\PostServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/compte/{username}/post")
 */
class PostController extends AbstractController
{

    private $postServices;

    public function __construct(PostServices $postServices)
    {
        $this->postServices = $postServices;
    }

    /********/
    /**
     * @Route("/{id}/like", name="app_post_like", methods={"GET"})
     */
    public function like($id, $username)
    {
        // on récupére le post correspondant à l'ID
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        if (!$post) {
            return $this->redirectToRoute('app_home');
        }

        // on récupère l'user connecté
        $user = $this->getUser();
        
       // on crée une nouvelle instance de Like et on associe le post et l'utilisateur
        $like = new Like();
        $like->setPost($post);
        $like->setUser($user);
        // on ajoute le like à l'entité Post
        $post->addLike($like);
        // on enregistrer les modifications dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($like);
        $entityManager->flush();

        // on rediriger l'utilisateur vers la page du post, par l'id et le username
        return $this->redirectToRoute('app_post_show', ['id' => $post->getId(), 'username' => $username]);
    }


    /**
     * @Route("/new", name="app_post_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PostServices $postServices): Response
    {
        $userConnected = $this->getUser();
        $response = $postServices->new($request, $userConnected);

        return $response;
    }

    /**
     * @Route("/{id}", name="app_post_show", methods={"GET"})
     */
    public function show(Post $post, $username): Response
    {
        // récupérer l'utilisateur correspondant à l'username
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);
        $postTags = $post->getPosttag();

        return $this->render('post/show.html.twig', [
            'user' => $user,
            'post' => $post,
            'postTags' => $postTags
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_post_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Post $post, $username): Response
    {
        $userConnected = $this->getUser();
        $users = $this->postServices->checkUsers($username, $userConnected);
        
        $userConnected = $users['userConnected'];

        return $this->postServices->editPost($request, $post, $userConnected);
    }

    /**
     * @Route("/{id}", name="app_post_delete", methods={"POST"})
     */
    public function delete(Post $post, $username): Response
    {
        // récupère l'utilisateur connecté
        $userConnected = $this->getUser();
        $users = $this->postServices->checkUsers($username, $userConnected);
        
        $userConnected = $users['userConnected'];
        $user = $users['user'];

        $this->postServices->deletePost($post, $userConnected);

        return $this->redirectToRoute('app_account', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);
    }
}

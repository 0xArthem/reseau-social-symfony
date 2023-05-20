<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Services\LikeServices;
use App\Services\PostServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/compte/{username}/post")
 */
class PostController extends AbstractController
{

    private $postServices;
    private $likeServices;
    private $postRepository;

    public function __construct(PostServices $postServices, LikeServices $likeServices, PostRepository $postRepository)
    {
        $this->postServices = $postServices;
        $this->likeServices = $likeServices;
        $this->postRepository = $postRepository;
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
     * @Route("/{slug}", name="app_post_show", methods={"GET"})
     */
    public function show($slug, $username, Security $security, PostRepository $postRepository): Response
    {
        // Récupérer l'utilisateur correspondant à l'username
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);
       
        $post = $postRepository->findOneBySlug($slug);

        $postTags = $post->getPosttag();
        

        $isLikedByUser = false;
        // on vérifie si l'utilisateur connecté a déjà liké le post
        $likes = $post->getLikes();
        foreach ($likes as $like) {
            if ($like->getUser() === $security->getUser()) {
                $isLikedByUser = true;
                break;
            }
        }

        // on récupère tous les likes du post
        $likesCount = count($post->getLikes());

        return $this->render('post/show.html.twig', [
            'user' => $user,
            'post' => $post,
            'postTags' => $postTags,
            'isLikedByUser' => $isLikedByUser,
            'likesCount' => $likesCount
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="app_post_edit", methods={"GET", "POST"})
     */
    public function edit($slug, Request $request): Response
    {
        $post = $this->postRepository->findOneBy(['slug' => $slug]);

        if ($post === null) {
            throw $this->createNotFoundException('Post not found');
        }

        $userConnected = $this->getUser();
        $users = $this->postServices->checkUsers($post->getUser()->getUsername(), $userConnected);
        $userConnected = $users['userConnected'];

        return $this->postServices->editPost($request, $post, $userConnected);
    }

    /**
     * @Route("/{slug}", name="app_post_delete", methods={"POST"})
     */
    public function delete(Post $post, $username): Response
    {
        // Récupère l'utilisateur connecté
        $userConnected = $this->getUser();
        $users = $this->postServices->checkUsers($post->getUser()->getUsername(), $userConnected);

        $userConnected = $users['userConnected'];
        $user = $users['user'];

        $this->postServices->deletePost($post, $userConnected);

        return $this->redirectToRoute('app_account', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);
    }

    /** système de likes **/

    /**
     * @Route("/{slug}/like", name="app_post_like", methods={"GET"})
     */
    public function like($slug, $username)
    {
        // On récupère le post correspondant au slug
        $post = $this->getDoctrine()->getRepository(Post::class)->findOneBy(['slug' => $slug]);

        if (!$post) {
            return $this->redirectToRoute('app_home');
        }

        // On récupère l'utilisateur connecté
        $user = $this->getUser();

        // On utilise le service LikeServices pour liker le post
        return $this->likeServices->like($post, $user, $username);
    }

    /**
     * @Route("/{slug}/dislike", name="app_post_dislike", methods={"GET"})
     */
    public function removeLike($slug, $username)
    {
        // On récupère le post correspondant au slug
        $post = $this->getDoctrine()->getRepository(Post::class)->findOneBy(['slug' => $slug]);

        if (!$post) {
            return $this->redirectToRoute('app_home');
        }

        // On récupère l'utilisateur connecté
        $user = $this->getUser();

        // On utilise le service LikeServices pour supprimer le like
        return $this->likeServices->removeLike($post, $user, $username);
    }
}

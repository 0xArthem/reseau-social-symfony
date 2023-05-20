<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use App\Services\LikeServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/like")
 */
class LikeController extends AbstractController
{
    private $likeServices;

    public function __construct(LikeServices $likeServices)
    {
        $this->likeServices = $likeServices;
    }

    /**
     * @Route("/{slug}/{username}", name="app_post_like", methods={"GET"})
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
     * @Route("/{slug}/{username}/dislike", name="app_post_dislike", methods={"GET"})
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


<?php

namespace App\Services;

use App\Entity\Like;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LikeServices
{
    private $entityManager;
    private $urlGenerator;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
    }
    
    public function like(Post $post, $user, $username)
    {
        // On contrôle si l'utilisateur a déjà liké le post pour ne pas qu'il puisse le liker
        $likes = $post->getLikes();
        foreach ($likes as $like) {
            if ($like->getUser() === $user) {
                return new RedirectResponse($this->urlGenerator->generate('app_post_show', ['id' => $post->getId(), 'username' => $username]));
            }
        }

        // On crée une nouvelle instance de Like et on associe le post et l'utilisateur
        $like = new Like();
        $like->setPost($post);
        $like->setUser($user);
        // On ajoute le like à l'entité Post
        $post->addLike($like);

        // On enregistre les modifications dans la base de données
        $this->entityManager->persist($like);
        $this->entityManager->flush();

        // On redirige l'utilisateur vers la page du post, par l'id et le username
        return new RedirectResponse($this->urlGenerator->generate('app_post_show', ['id' => $post->getId(), 'username' => $username]));
    }

    public function removeLike(Post $post, $user, $username)
    {
        // On recherche le "like" associé au post et à l'utilisateur
        $like = $this->entityManager->getRepository(Like::class)->findOneBy([
            'post' => $post,
            'user' => $user
        ]);

        // Si le "like" existe, le supprimer
        if ($like) {
            $post->removeLike($like);

            $this->entityManager->remove($like);
            $this->entityManager->flush();
        }

        // Rediriger l'utilisateur vers la page du post, par l'id et le username
        return new RedirectResponse($this->urlGenerator->generate('app_post_show', ['id' => $post->getId(), 'username' => $username]));
    }
}

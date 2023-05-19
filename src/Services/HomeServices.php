<?php

namespace App\Services;

use Twig\Environment;
use App\Repository\PostRepository;
use App\Repository\PostTagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;


class HomeServices {
    private $postRepository;
    private $postTagRepository;
    private $paginator;
    private $twig;

    public function __construct(PostRepository $postRepository, PostTagRepository $postTagRepository, PaginatorInterface $paginator, Environment $twig)
    {
        $this->postRepository = $postRepository;
        $this->postTagRepository = $postTagRepository;
        $this->paginator = $paginator;
        $this->twig = $twig;
    }
    
    public function renderForConnectedUser(UserInterface $user, Request $request): Response
    {
        $abonnements = $user->getAbonnements();
        $usersAbonnement = [];

        foreach ($abonnements as $abonnement) {
            $usersAbonnement[] = $abonnement->getAbonnement();
        }

        $postTags = $this->postTagRepository->findAll();
        $query = $this->postRepository->findBy(['user' => $usersAbonnement], ['createdAt' => 'DESC']);

        $page = $request->query->get('page', 1);
        $posts = $this->paginator->paginate(
            $query,
            $page,
            12
        );

        $mostLikedPosts = $this->postRepository->findMostLikedPosts(12);

        return new Response($this->twig->render('home/index.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'abonnements' => $abonnements,
            'postTags' => $postTags,
            'mostLikedPosts' => $mostLikedPosts,
            'page' => $page
        ]));
    }

    public function renderForVisitedUser(Request $request): Response
    {
        $postTags = $this->postTagRepository->findAll();

        $query = $this->postRepository->findBy(array(), array('createdAt' => 'DESC'));
        $page = $request->query->get('page', 1);
        $posts = $this->paginator->paginate(
            $query,
            $page,
            12
        );

        $mostLikedPosts = $this->postRepository->findMostLikedPosts(12);

        return new Response($this->twig->render('home/index-other.html.twig', [
            'posts' => $posts,
            'postTags' => $postTags,
            'mostLikedPosts' => $mostLikedPosts,
            'page' => $page
        ]));
    }

    public function nouveautes(Request $request): Response
    {
        $postTags = $this->postTagRepository->findAll();

        $query = $this->postRepository->findBy(array(), array('createdAt' => 'DESC'));
        $posts = $this->paginator->paginate(
            $query,
            $request->query->get('page', 1),
            9
        );

        $content = $this->twig->render('home/nouveautes.html.twig', [
            'posts' => $posts,
            'postTags' => $postTags
        ]);

        return new Response($content);
    }

    public function byPostTag($slug, Request $request): Response
    {
        $postTag = $this->postTagRepository->findOneBySlug($slug);
        $postTags = $this->postTagRepository->findAll();

        $query = $postTag->getPosts();

        $posts = $this->paginator->paginate(
            $query,
            $request->query->get('page', 1),
            9
        );

        $content = $this->twig->render('post/byTag.html.twig', [
            'posts' => $posts,
            'postTag' => $postTag,
            'postTags' => $postTags,
            'query' => $query,
        ]);

        return new Response($content);
    }

    public function searchPosts(Request $request): Response
    {
        $query = $request->query->get('q');
        $postTags = $this->postTagRepository->findAll();

        $q = $this->postRepository->searchByPost($query);

        $posts = $this->paginator->paginate(
            $q,
            $request->query->get('page', 1),
            9
        );

        $content = $this->twig->render('post/bySearch.html.twig', [
            'posts' => $posts,
            'query' => $query,
            'postTags' => $postTags
        ]);

        return new Response($content);
    }
}
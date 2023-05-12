<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Repository\PostTagRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $postRepository;
    private $postTagRepository;
    private $paginator;
    
    public function __construct(PostRepository $postRepository, PostTagRepository $postTagRepository, PaginatorInterface $paginator)
    {
        $this->postRepository = $postRepository;
        $this->postTagRepository = $postTagRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(Security $security, Request $request): Response
    {
        $user = $security->getUser();
        
        if ($user) {
            return $this->renderForConnectedUser($user, $request, $this->postRepository, $this->postTagRepository);
        } else {
            return $this->renderForVisitedUser($request, $this->postRepository, $this->postTagRepository);
        }
    }

    private function renderForConnectedUser(UserInterface $user, Request $request): Response
    {
        $abonnements = $user->getAbonnements();
        $usersAbonnement = [];

        foreach ($abonnements as $abonnement) {
            $usersAbonnement[] = $abonnement->getAbonnement();
        }

        $postTags = $this->postTagRepository->findAll();
        $query = $this->postRepository->findBy(['user' => $usersAbonnement], ['createdAt' => 'DESC']);

        $posts = $this->paginator->paginate(
            $query,
            $request->query->get('page', 1),
            12
        );

        return $this->render('home/index.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'abonnements' => $abonnements,
            'postTags' => $postTags
        ]);
    }

    private function renderForVisitedUser(Request $request): Response
    {
        $postTags = $this->postTagRepository->findAll();

        $query = $this->postRepository->findBy(array(), array('createdAt' => 'DESC'));
        $posts = $this->paginator->paginate(
            $query,
            $request->query->get('page', 1),
            12
        );

        return $this->render('home/index-other.html.twig', [
            'posts' => $posts,
            'postTags' => $postTags
        ]);
    }
    
    /**
     * @Route("/posts/recherche/tag/{slug}", name="posts_tag")
     */
    public function byPostTag($slug, Request $request): Response
    {
        $postTag = $this->postTagRepository->findOneBySlug($slug);
        $postTags = $this->postTagRepository->findAll();

        $query = $postTag->getPosts();

        $posts = $this->paginator->paginate(
            $query,
            $request->query->get('page', 1),
            12
        );

        return $this->render('post/byTag.html.twig', [
            'posts' => $posts,
            'postTag' => $postTag,
            'postTags' => $postTags,
            'query' => $query,
        ]);
    }

    /**
     * @Route("/posts/recherche", name="post_search")
     */
    public function searchPosts(Request $request, PostRepository $postRepository, PostTagRepository $postTagRepository): Response
    {
        $query = $request->query->get('q');
        $postTags = $postTagRepository->findAll();

        $q = $postRepository->searchByPost($query);

        $posts = $this->paginator->paginate(
            $q,
            $request->query->get('page', 1),
            12
        );

        return $this->render('post/bySearch.html.twig', [
            'posts' => $posts,
            'query' => $query,
            'postTags' => $postTags
        ]);
    }
}

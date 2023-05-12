<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\PostTagRepository;
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
    
    public function __construct(PostRepository $postRepository, PostTagRepository $postTagRepository)
    {
        $this->postRepository = $postRepository;
        $this->postTagRepository = $postTagRepository;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(Security $security): Response
    {
        $user = $security->getUser();
        
        if ($user) {
            return $this->renderForConnectedUser($user, $this->postRepository, $this->postTagRepository);
        } else {
            return $this->renderForVisitedUser($this->postRepository, $this->postTagRepository);
        }
    }

    private function renderForConnectedUser(UserInterface $user): Response
    {
        $abonnements = $user->getAbonnements();
        $usersAbonnement = [];

        foreach ($abonnements as $abonnement) {
            $usersAbonnement[] = $abonnement->getAbonnement();
        }

        $posts = $this->postRepository->findBy(['user' => $usersAbonnement], ['id' => 'DESC']);
        $postTags = $this->postTagRepository->findAll();

        return $this->render('home/index.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'abonnements' => $abonnements,
            'postTags' => $postTags
        ]);
    }

    private function renderForVisitedUser(): Response
    {
        $posts = $this->postRepository->findBy([], ['id' => 'DESC']);
        $postTags = $this->postTagRepository->findAll();

        return $this->render('home/index-other.html.twig', [
            'posts' => $posts,
            'postTags' => $postTags
        ]);
    }
    
    /**
     * @Route("/posts/recherche/tag/{slug}", name="posts_tag")
     */
    public function byPostTag($slug): Response
    {
        $postTag = $this->postTagRepository->findOneBySlug($slug);
        $posts = $postTag->getPosts();
        $postTags = $this->postTagRepository->findAll();


        return $this->render('post/byTag.html.twig', [
            'posts' => $posts,
            'postTag' => $postTag,
            'postTags' => $postTags
        ]);
    }

    /**
     * @Route("/posts/recherche", name="post_search")
     */
    public function searchPosts(Request $request, PostRepository $postRepository, PostTagRepository $postTagRepository): Response
    {
        $query = $request->query->get('q');
        $posts = $postRepository->searchByPost($query);

        $postTags = $postTagRepository->findAll();

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'postTags' => $postTags
        ]);
    }
}

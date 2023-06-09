<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\InfoRepository;
use App\Repository\ProductRepository;
use App\Services\HomeServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $homeServices;
    private $infoRepository;
    private $articleRepository;
    private $productRepository;
    
    public function __construct(HomeServices $homeServices, ProductRepository $productRepository, InfoRepository $infoRepository, ArticleRepository $articleRepository)
    {
        $this->homeServices = $homeServices;
        $this->infoRepository = $infoRepository;
        $this->articleRepository = $articleRepository;
        $this->productRepository = $productRepository;

    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(Security $security, Request $request): Response
    {
        $user = $security->getUser();
        $infos = $this->infoRepository->findBy(array(), array('id' => 'DESC'), 1, 0);
        $articles = $this->articleRepository->findBy(array('isActive' => True), array('id' => 'DESC'));
        $products = $this->productRepository->findBy(array('online' => True), array('id' => 'DESC'), 1, 0);
        
        if ($user) {
            return $this->homeServices->renderForConnectedUser($user, $infos, $articles, $products, $request);
        } else {
            return $this->homeServices->renderForVisitedUser($request, $infos, $articles, $products);
        }
    }
    
    /**
     * @Route("/posts/nouveautes", name="app_home_news")
     */
    public function nouveautes(Request $request): Response
    {
        return $this->homeServices->nouveautes($request);
    }
    
    /**
     * @Route("/posts/recherche/tag/{slug}", name="posts_tag")
     */
    public function byPostTag($slug, Request $request): Response
    {
        return $this->homeServices->byPostTag($slug, $request);
    }

    /**
     * @Route("/posts/recherche", name="post_search")
     */
    public function searchPosts(Request $request): Response
    {
        return $this->homeServices->searchPosts($request);
    }
}

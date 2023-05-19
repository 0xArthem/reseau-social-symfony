<?php

namespace App\Controller;

use App\Services\HomeServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $homeServices;
    
    public function __construct(HomeServices $homeServices)
    {
        $this->homeServices = $homeServices;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(Security $security, Request $request): Response
    {
        $user = $security->getUser();
        
        if ($user) {
            return $this->homeServices->renderForConnectedUser($user, $request);
        } else {
            return $this->homeServices->renderForVisitedUser($request);
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
